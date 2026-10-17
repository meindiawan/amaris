<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Daftar Booking Order: admin memilih unit (nopol) & supir yang tersedia, mengisi detail
 * pelayanan/giat/standby/tujuan/kontak tamu, lalu membagikan teks "Booking Order" ke klien via WhatsApp.
 */
class OrderController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->query('status');
        $q = trim((string) $r->query('q'));
        $orders = Booking::with(['vehicle', 'driver'])
            ->when(isset(Booking::STATUSES[$status]), fn ($x) => $x->where('status', $status))
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('customer_name', 'like', "%$q%")->orWhere('order_name', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->orWhere('car_name', 'like', "%$q%")->orWhere('giat', 'like', "%$q%")))
            ->orderByRaw("case status when 'pending' then 0 when 'confirmed' then 1 when 'ongoing' then 2 else 3 end")
            ->orderByDesc('start_at')->paginate(12)->withQueryString();
        $counts = Booking::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        return view('admin.orders.index', compact('orders', 'counts', 'status', 'q'));
    }

    public function create()
    {
        $b = new Booking(['status' => 'confirmed', 'start_at' => now()->setTime(8, 0), 'end_at' => now()->setTime(17, 0), 'service_type' => 'with_driver']);
        return view('admin.orders.form', $this->formData($b));
    }

    public function edit(Booking $order) { return view('admin.orders.form', $this->formData($order)); }

    public function store(Request $r)
    {
        $d = $this->validated($r, null);
        $v = !empty($d['vehicle_id']) ? Vehicle::find($d['vehicle_id']) : null;
        $d += [
            'code' => Booking::makeCode(), 'car_id' => $v?->car_id, 'car_name' => $v?->label ?: 'Booking Order',
            'service_type' => 'with_driver', 'customer_name' => $d['order_name'], 'phone' => $d['guest_phone'] ?: '-',
            'nik' => '-', 'address' => '-', 'pickup_method' => 'office', 'return_method' => 'office',
        ];
        $b = Booking::create($d);
        return redirect()->route('admin.orders.index')->with('ok', 'Booking Order '.$b->code.' dibuat.');
    }

    public function update(Request $r, Booking $order)
    {
        $d = $this->validated($r, $order);
        if (!empty($d['vehicle_id']) && ($v = Vehicle::find($d['vehicle_id'])) && $v->car_id && !$order->car_id) $d['car_id'] = $v->car_id;
        $order->update($d);
        return redirect()->route('admin.orders.index')->with('ok', 'Booking Order '.$order->code.' disimpan.');
    }

    public function destroy(Booking $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('ok', 'Booking dihapus.');
    }

    private function formData(Booking $b): array
    {
        $busy = Booking::whereIn('status', Booking::ASSIGNING)->where('end_at', '>=', now()->subDay())
            ->where(fn ($q) => $q->whereNotNull('vehicle_id')->orWhereNotNull('driver_id'))
            ->when($b->exists, fn ($q) => $q->where('id', '!=', $b->id))
            ->get(['vehicle_id', 'driver_id', 'start_at', 'end_at', 'code'])
            ->map(fn ($x) => ['v' => $x->vehicle_id, 'd' => $x->driver_id, 's' => $x->start_at->format('Y-m-d\TH:i'), 'e' => $x->end_at->format('Y-m-d\TH:i'), 'c' => $x->code]);
        return [
            'order' => $b, 'busy' => $busy,
            'vehicles' => Vehicle::with('car')->where(fn ($q) => $q->where('is_active', true)->orWhere('id', $b->vehicle_id))->orderBy('plate_number')->get(),
            'drivers' => Driver::where(fn ($q) => $q->where('is_active', true)->orWhere('id', $b->driver_id))->orderBy('name')->get(),
        ];
    }

    private function validated(Request $r, ?Booking $b): array
    {
        $d = $r->validate([
            'order_name' => 'required|max:150',
            'start_date' => 'required|date', 'start_time' => 'required|date_format:H:i', 'end_date' => 'required|date|after_or_equal:start_date', 'end_time' => 'required|date_format:H:i',
            'vehicle_id' => 'nullable|exists:vehicles,id', 'driver_id' => 'nullable|exists:drivers,id',
            'pelayanan' => 'nullable|max:150', 'giat' => 'nullable|max:150', 'standby_location' => 'nullable|max:200', 'standby_time' => 'nullable|max:30',
            'destination' => 'nullable|max:200', 'guest_name' => 'nullable|max:120', 'guest_phone' => 'nullable|max:30',
            'status' => 'required|in:'.implode(',', array_keys(Booking::STATUSES)), 'admin_notes' => 'nullable|max:2000',
        ], ['order_name.required' => 'Nama pada Booking Order wajib diisi.', 'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.']);

        $start = Carbon::parse($d['start_date'].' '.$d['start_time']);
        $end = Carbon::parse($d['end_date'].' '.$d['end_time']);
        if ($end->lessThanOrEqualTo($start)) abort(back()->withInput()->withErrors(['end_date' => 'Waktu selesai harus setelah waktu mulai.']));

        $active = in_array($d['status'], Booking::BLOCKING) || $d['status'] === 'pending';
        $id = $b?->id;
        if ($active && !empty($d['vehicle_id']) && Booking::vehicleConflicts((int) $d['vehicle_id'], $start, $end, $id)->exists()) {
            abort(back()->withInput()->withErrors(['vehicle_id' => 'Armada ini sudah dipakai booking lain pada tanggal tersebut. Pilih armada yang tersedia.']));
        }
        if ($active && !empty($d['driver_id']) && Booking::driverConflicts((int) $d['driver_id'], $start, $end, $id)->exists()) {
            abort(back()->withInput()->withErrors(['driver_id' => 'Supir ini sudah bertugas di booking lain pada tanggal tersebut. Pilih supir yang tersedia.']));
        }
        if (in_array($d['status'], Booking::BLOCKING) && $b && $b->car_id && empty($d['vehicle_id'])
            && Booking::conflicts($b->car_id, $start, $end, $id)->exists()) {
            abort(back()->withInput()->withErrors(['status' => 'Tidak bisa dikonfirmasi: jadwal bentrok dengan booking lain yang sudah dikonfirmasi untuk mobil ini.']));
        }

        unset($d['start_date'], $d['start_time'], $d['end_date'], $d['end_time']);
        $d['start_at'] = $start; $d['end_at'] = $end;
        $d['days'] = max(1, (int) ceil($start->diffInMinutes($end) / 1440));
        foreach (['vehicle_id', 'driver_id'] as $k) $d[$k] = $d[$k] ?? null;
        return $d;
    }
}
