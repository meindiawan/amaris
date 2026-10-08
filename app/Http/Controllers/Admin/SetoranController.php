<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Setoran;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index(Request $r)
    {
        $base = Setoran::query()
            ->when($r->filled('from'), fn ($q) => $q->whereDate('paid_at', '>=', $r->query('from')))
            ->when($r->filled('to'), fn ($q) => $q->whereDate('paid_at', '<=', $r->query('to')))
            ->when($r->filled('vehicle'), fn ($q) => $q->where('vehicle_id', $r->query('vehicle')))
            ->when($r->filled('kind'), fn ($q) => $q->where('kind', $r->query('kind')));
        $total = (clone $base)->sum('amount');
        $perVehicle = (clone $base)->whereNotNull('vehicle_id')->selectRaw('vehicle_id, sum(amount) as total')->groupBy('vehicle_id')->with('vehicle')->get();
        $rows = $base->with(['vehicle', 'driver', 'booking'])->orderByDesc('paid_at')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.setorans.index', ['rows' => $rows, 'total' => $total, 'perVehicle' => $perVehicle, 'vehicles' => Vehicle::orderBy('plate_number')->get()]);
    }
    public function create(Request $r) { return view('admin.setorans.form', $this->lists(new Setoran(['paid_at' => now(), 'kind' => 'unit', 'method' => 'tunai', 'vehicle_id' => $r->query('vehicle'), 'booking_id' => $r->query('booking')]))); }
    public function store(Request $r) { $d = $this->data($r); $d['received_by'] = auth()->user()->name; Setoran::create($d); return redirect()->route('admin.setoran.index')->with('ok', 'Setoran dicatat.'); }
    public function edit(Setoran $setoran) { return view('admin.setorans.form', $this->lists($setoran)); }
    public function update(Request $r, Setoran $setoran) { $setoran->update($this->data($r)); return redirect()->route('admin.setoran.index')->with('ok', 'Setoran diperbarui.'); }
    public function destroy(Setoran $setoran) { $setoran->delete(); return back()->with('ok', 'Setoran dihapus.'); }

    private function lists(Setoran $s): array
    {
        return ['row' => $s, 'vehicles' => Vehicle::orderBy('plate_number')->get(), 'drivers' => Driver::orderBy('name')->get(), 'bookings' => Booking::latest()->take(100)->get()];
    }
    private function data(Request $r): array
    {
        $d = $r->validate([
            'kind' => 'required|in:unit,supir,lainnya', 'amount' => 'required|integer|min:1', 'paid_at' => 'required|date',
            'method' => 'required|in:tunai,transfer,lainnya', 'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id', 'booking_id' => 'nullable|exists:bookings,id', 'note' => 'nullable|max:1000',
        ], ['amount.required' => 'Jumlah setoran wajib diisi.']);
        foreach (['vehicle_id', 'driver_id', 'booking_id'] as $k) $d[$k] = $d[$k] ?? null;
        return $d;
    }
}
