<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Vehicle;
use App\Support\Fmt;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));
        $f = $r->query('f');
        $vehicles = Vehicle::with('car')
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('plate_number', 'like', "%$q%")->orWhere('display_name', 'like', "%$q%")->orWhere('barcode', 'like', "%$q%")->orWhere('owner_name', 'like', "%$q%")))
            ->when($f === 'out', fn ($x) => $x->where('is_out', true))
            ->when($f === 'in', fn ($x) => $x->where('is_out', false)->where('is_active', true))
            ->when($f === 'off', fn ($x) => $x->where('is_active', false))
            ->orderBy('plate_number')->get();
        return view('admin.vehicles.index', compact('vehicles', 'q', 'f'));
    }

    public function create() { return view('admin.vehicles.form', ['vehicle' => new Vehicle(['is_active' => true, 'owner_type' => 'own']), 'cars' => Car::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $d = $this->data($r);
        $v = Vehicle::create($d);
        return redirect()->route('admin.vehicles.show', $v)->with('ok', 'Unit '.$v->plate_number.' ditambahkan. Kode barcode: '.$v->barcode);
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('car');
        $moves = $vehicle->movements()->with(['driver', 'booking'])->take(15)->get();
        $bookings = $vehicle->bookings()->latest('start_at')->take(10)->get();
        $setoranTotal = $vehicle->setorans()->sum('amount');
        return view('admin.vehicles.show', compact('vehicle', 'moves', 'bookings', 'setoranTotal'));
    }

    public function edit(Vehicle $vehicle) { return view('admin.vehicles.form', ['vehicle' => $vehicle, 'cars' => Car::orderBy('name')->get()]); }

    public function update(Request $r, Vehicle $vehicle)
    {
        $vehicle->update($this->data($r, $vehicle));
        return redirect()->route('admin.vehicles.show', $vehicle)->with('ok', 'Data unit diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('ok', 'Unit dihapus.');
    }

    public function label(Vehicle $vehicle) { return view('admin.vehicles.label', ['vehicles' => collect([$vehicle])]); }
    public function labels() { return view('admin.vehicles.label', ['vehicles' => Vehicle::where('is_active', true)->orderBy('plate_number')->get()]); }

    private function data(Request $r, ?Vehicle $v = null): array
    {
        $plate = Vehicle::normalizePlate($r->input('plate_number'));
        $r->merge(['plate_number' => $plate]);
        $d = $r->validate([
            'plate_number' => 'required|max:20|unique:vehicles,plate_number'.($v ? ','.$v->id : ''),
            'car_id' => 'nullable|exists:cars,id', 'display_name' => 'nullable|max:120',
            'year' => 'nullable|integer|min:1980|max:2100', 'color' => 'nullable|max:40',
            'chassis_number' => 'nullable|max:40', 'engine_number' => 'nullable|max:40',
            'stnk_number' => 'nullable|max:40', 'stnk_owner' => 'nullable|max:120',
            'stnk_expiry' => 'nullable|date', 'tax_expiry' => 'nullable|date',
            'owner_type' => 'nullable|in:own,partner', 'owner_name' => 'nullable|max:120', 'owner_phone' => 'nullable|max:30',
            'setoran_per_day' => 'nullable|integer|min:0', 'odometer' => 'nullable|integer|min:0',
            'notes' => 'nullable|max:2000', 'stnk_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
            'fuel_barcode_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
        ], ['plate_number.required' => 'Plat nomor wajib diisi.', 'plate_number.unique' => 'Plat nomor ini sudah terdaftar.']);
        $d['setoran_per_day'] = (int) ($d['setoran_per_day'] ?? 0);
        $d['owner_type'] = $d['owner_type'] ?? 'own';
        $d['is_active'] = $r->has('is_active') ? $r->boolean('is_active') : true;
        unset($d['stnk_file'], $d['fuel_barcode_file']);
        if ($r->hasFile('stnk_file')) $d['stnk_file'] = Fmt::saveUpload($r->file('stnk_file'), 'stnk');
        if ($r->hasFile('fuel_barcode_file')) $d['fuel_barcode_file'] = Fmt::saveUpload($r->file('fuel_barcode_file'), 'fuel-barcode');
        return $d;
    }
}
