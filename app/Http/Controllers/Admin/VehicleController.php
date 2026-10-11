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
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('plate_number', 'like', "%$q%")->orWhere('display_name', 'like', "%$q%")->orWhere('barcode', 'like', "%$q%")))
            ->when($f === 'out', fn ($x) => $x->where('is_out', true))
            ->when($f === 'in', fn ($x) => $x->where('is_out', false)->where('is_active', true))
            ->when($f === 'off', fn ($x) => $x->where('is_active', false))
            ->orderBy('plate_number')->get();
        return view('admin.vehicles.index', compact('vehicles', 'q', 'f'));
    }

    public function create() { return view('admin.vehicles.form', ['vehicle' => new Vehicle(['is_active' => true]), 'cars' => Car::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $d = $this->data($r);
        $v = Vehicle::create($d);
        return redirect()->route('admin.vehicles.show', $v)->with('ok', 'Unit '.$v->plate_number.' ditambahkan. Kode barcode: '.$v->barcode);
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('car');
        $logs = \App\Models\VehicleLog::where('vehicle_id', $vehicle->id)->orderByDesc('out_date')->orderByDesc('id')->take(15)->get();
        $bookings = $vehicle->bookings()->latest('start_at')->take(10)->get();
        return view('admin.vehicles.show', compact('vehicle', 'logs', 'bookings'));
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

    /** Form armada hanya: nopol, jenis mobil, file STNK, file barcode BBM. */
    private function data(Request $r, ?Vehicle $v = null): array
    {
        $r->merge(['plate_number' => Vehicle::normalizePlate($r->input('plate_number'))]);
        $d = $r->validate([
            'plate_number' => 'required|max:20|unique:vehicles,plate_number'.($v ? ','.$v->id : ''),
            'display_name' => 'required|max:120',
            'stnk_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
            'barcode_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
        ], [
            'plate_number.required' => 'Nopol wajib diisi.', 'plate_number.unique' => 'Nopol ini sudah terdaftar.',
            'display_name.required' => 'Jenis mobil wajib diisi.',
            'stnk_file.mimes' => 'File STNK harus berupa JPG, PNG, WEBP atau PDF.', 'barcode_image.mimes' => 'File barcode BBM harus berupa JPG, PNG, WEBP atau PDF.',
        ]);
        unset($d['stnk_file'], $d['barcode_image']);
        $d['is_active'] = $r->boolean('is_active');
        // Hubungkan otomatis ke katalog website bila nama jenis mobil cocok (untuk penugasan booking).
        $name = mb_strtolower(trim($d['display_name']));
        $car = Car::all()->first(fn ($c) => ($cn = mb_strtolower($c->name)) === $name || str_contains($cn, $name) || str_contains($name, $cn));
        $d['car_id'] = $car?->id;
        if ($r->hasFile('stnk_file')) $d['stnk_file'] = Fmt::saveUpload($r->file('stnk_file'), 'stnk');
        if ($r->hasFile('barcode_image')) $d['barcode_image'] = Fmt::saveUpload($r->file('barcode_image'), 'barcode-bbm');
        return $d;
    }
}
