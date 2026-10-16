<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use App\Support\Fmt;
use Illuminate\Http\Request;

/** Mobil keluar / masuk — satu baris = satu perjalanan (nopol, rental, tgl keluar/masuk, booking, invoice, payment, driver). */
class MovementController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));
        $f = $r->query('f');
        $vehicles = Vehicle::where('is_active', true)->orderBy('plate_number')->get();
        $logs = VehicleLog::with(['vehicle', 'driver'])
            ->when($r->filled('vehicle'), fn ($x) => $x->where('vehicle_id', $r->query('vehicle')))
            ->when($r->filled('date'), fn ($x) => $x->where(fn ($w) => $w->whereDate('out_date', $r->query('date'))->orWhereDate('in_date', $r->query('date'))))
            ->when($f === 'out', fn ($x) => $x->whereNull('in_date'))
            ->when($f === 'unpaid', fn ($x) => $x->where('payment_status', 'belum'))
            ->when($f === 'noinvoice', fn ($x) => $x->where('invoice_status', '!=', 'sudah_terkirim'))
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('plate_number', 'like', "%$q%")->orWhere('vehicle_type', 'like', "%$q%")
                ->orWhere('rental_name', 'like', "%$q%")->orWhere('booker_name', 'like', "%$q%")->orWhere('driver_name', 'like', "%$q%")->orWhere('notes', 'like', "%$q%")))
            ->orderByDesc('out_date')->orderByDesc('id')->paginate(25)->withQueryString();
        $counts = [
            'all' => VehicleLog::count(), 'out' => VehicleLog::whereNull('in_date')->count(),
            'unpaid' => VehicleLog::where('payment_status', 'belum')->count(), 'noinvoice' => VehicleLog::where('invoice_status', '!=', 'sudah_terkirim')->count(),
        ];
        return view('admin.movements.index', compact('vehicles', 'logs', 'q', 'f', 'counts'));
    }

    /** Hasil scan barcode (atau ketik plat) → form catat keluar/masuk. */
    public function scan(Request $r)
    {
        $v = Vehicle::findByScan((string) $r->query('code'));
        if (!$v) return redirect()->route('admin.movements.index')->withErrors(['code' => 'Unit dengan kode / plat "'.$r->query('code').'" tidak ditemukan.']);
        return redirect()->route('admin.movements.create', ['vehicle' => $v->id]);
    }

    public function create(Request $r)
    {
        $log = new VehicleLog(['out_date' => now()->toDateString(), 'invoice_status' => 'belum_dibuat', 'payment_status' => 'belum']);
        if ($r->filled('vehicle')) $log->vehicle_id = (int) $r->query('vehicle');
        return view('admin.movements.form', $this->formData($log));
    }

    public function edit(VehicleLog $movement) { return view('admin.movements.form', $this->formData($movement)); }

    public function store(Request $r)
    {
        $log = VehicleLog::create($this->data($r) + ['recorded_by' => auth()->user()->name]);
        return redirect()->route('admin.movements.index')->with('ok', 'Catatan '.$log->plate_number.' disimpan.');
    }

    public function update(Request $r, VehicleLog $movement)
    {
        $movement->update($this->data($r));
        return redirect()->route('admin.movements.index')->with('ok', 'Catatan '.$movement->plate_number.' diperbarui.');
    }

    /** Ubah cepat dari tabel: status invoice / payment (+ catatan penerima). */
    public function quick(Request $r, VehicleLog $movement)
    {
        $d = $r->validate([
            'invoice_status' => 'nullable|in:'.implode(',', array_keys(VehicleLog::INVOICE)),
            'payment_status' => 'nullable|in:'.implode(',', array_keys(VehicleLog::PAYMENT)),
            'payment_note'   => 'nullable|max:200',
            'in_date'        => 'nullable|date|after_or_equal:'.$movement->out_date->toDateString(),
        ], ['in_date.after_or_equal' => 'Tanggal masuk tidak boleh sebelum tanggal keluar.']);
        $upd = array_filter(['invoice_status' => $d['invoice_status'] ?? null, 'payment_status' => $d['payment_status'] ?? null, 'in_date' => $d['in_date'] ?? null], fn ($x) => $x !== null);
        if (($upd['payment_status'] ?? null) === 'sudah') {
            if (trim((string) ($d['payment_note'] ?? '')) === '' && !$movement->payment_note) return back()->withErrors(['payment_note' => 'Isi catatan: siapa yang menerima payment.']);
            if (!empty($d['payment_note'])) $upd['payment_note'] = $d['payment_note'];
        }
        if (($upd['payment_status'] ?? null) === 'belum') $upd['payment_note'] = null;
        $movement->update($upd);
        return back()->with('ok', 'Catatan '.$movement->plate_number.' diperbarui.');
    }

    public function destroy(VehicleLog $movement)
    {
        $movement->delete();
        return back()->with('ok', 'Catatan dihapus.');
    }

    private function formData(VehicleLog $log): array
    {
        $key = $log->exists ? ($log->vehicle_id ?: 'other') : ($log->vehicle_id ?: '');
        $dkey = $log->exists ? ($log->driver_id ?: ($log->driver_name ? 'other' : '')) : '';
        return [
            'log' => $log, 'vehicleKey' => old('vehicle_key', $key), 'driverKey' => old('driver_key', $dkey),
            'vehicles' => Vehicle::where('is_active', true)->orWhere('id', $log->vehicle_id)->orderBy('plate_number')->get(),
            'drivers' => Driver::where('is_active', true)->orWhere('id', $log->driver_id)->orderBy('name')->get(),
            'bookers' => Booking::orderByDesc('id')->take(60)->get(['customer_name', 'order_name'])->flatMap(fn ($b) => [$b->order_name, $b->customer_name])->filter()->unique()->values(),
        ];
    }

    private function data(Request $r): array
    {
        $d = $r->validate([
            'vehicle_key' => 'required', 'plate_number' => 'required_if:vehicle_key,other|nullable|max:20',
            'vehicle_type' => 'required_if:vehicle_key,other|nullable|max:120', 'rental_name' => 'required_if:vehicle_key,other|nullable|max:120',
            'out_date' => 'required|date', 'in_date' => 'nullable|date|after_or_equal:out_date',
            'booker_name' => 'nullable|max:150',
            'invoice_status' => 'required|in:'.implode(',', array_keys(VehicleLog::INVOICE)),
            'payment_status' => 'required|in:'.implode(',', array_keys(VehicleLog::PAYMENT)),
            'payment_note' => 'required_if:payment_status,sudah|nullable|max:200',
            'driver_key' => 'nullable', 'driver_name' => 'required_if:driver_key,other|nullable|max:120',
            'notes' => 'nullable|max:2000',
        ], [
            'vehicle_key.required' => 'Pilih nopol (atau "Lainnya").',
            'plate_number.required_if' => 'Nopol wajib diisi.', 'vehicle_type.required_if' => 'Jenis kendaraan wajib diisi.', 'rental_name.required_if' => 'Nama rental wajib diisi.',
            'out_date.required' => 'Tanggal keluar wajib diisi.', 'in_date.after_or_equal' => 'Tanggal masuk tidak boleh sebelum tanggal keluar.',
            'payment_note.required_if' => 'Isi catatan: siapa yang menerima payment.', 'driver_name.required_if' => 'Nama driver wajib diisi.',
        ]);

        if (ctype_digit((string) $d['vehicle_key']) && ($v = Vehicle::find((int) $d['vehicle_key']))) {
            $d['vehicle_id'] = $v->id; $d['plate_number'] = $v->plate_number; $d['vehicle_type'] = $v->label;
            $d['rental_name'] = trim((string) ($d['rental_name'] ?? '')) ?: Fmt::setting('company_name', 'Amaris Rent Car');
        } else {
            $d['vehicle_id'] = null; $d['plate_number'] = Vehicle::normalizePlate($d['plate_number'] ?? '');
        }

        $dk = (string) ($d['driver_key'] ?? '');
        if (ctype_digit($dk) && ($dr = Driver::find((int) $dk))) { $d['driver_id'] = $dr->id; $d['driver_name'] = $dr->name; }
        elseif ($dk === 'other') { $d['driver_id'] = null; }
        else { $d['driver_id'] = null; $d['driver_name'] = null; }

        if ($d['payment_status'] !== 'sudah') $d['payment_note'] = null;
        unset($d['vehicle_key'], $d['driver_key']);
        return $d;
    }
}
