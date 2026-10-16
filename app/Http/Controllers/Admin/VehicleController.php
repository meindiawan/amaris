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
        $vehicles = Vehicle::with('car')
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('plate_number', 'like', "%$q%")->orWhere('display_name', 'like', "%$q%")->orWhere('barcode', 'like', "%$q%")))
            ->orderBy('plate_number')->get();
        return view('admin.vehicles.index', compact('vehicles', 'q'));
    }

    public function create() { return view('admin.vehicles.form', ['vehicle' => new Vehicle(['is_active' => true]), 'cars' => Car::orderBy('name')->get()]); }

    public function store(Request $r)
    {
        $d = $this->data($r);
        $v = Vehicle::create($d);
        return redirect()->route('admin.vehicles.show', $v)->with('ok', 'Unit '.$v->plate_number.' ditambahkan. Kode barcode: '.$v->barcode);
    }

    public function show(Request $r, Vehicle $vehicle)
    {
        $vehicle->load('car');
        $from = $this->dateOrNull($r->query('from')); $to = $this->dateOrNull($r->query('to'));
        $logs = $this->logQuery($vehicle, $from, $to)->paginate(25)->withQueryString();
        $bookings = $vehicle->bookings()->latest('start_at')->take(10)->get();
        return view('admin.vehicles.show', compact('vehicle', 'logs', 'bookings', 'from', 'to'));
    }

    /** Riwayat keluar/masuk satu unit → file Excel (.xlsx). Bila ekstensi zip PHP tidak aktif, otomatis jadi CSV. */
    public function export(Request $r, Vehicle $vehicle)
    {
        $from = $this->dateOrNull($r->query('from')); $to = $this->dateOrNull($r->query('to'));
        $logs = $this->logQuery($vehicle, $from, $to)->get();
        $headers = ['No', 'Nopol', 'Jenis kendaraan', 'Tanggal keluar', 'Tanggal masuk', 'Dibawa oleh (driver)', 'Rental', 'User yang booking', 'Keterangan'];
        $rows = [];
        foreach ($logs as $i => $m) {
            $rows[] = [$i + 1, $m->plate_number, $m->vehicle_type ?: $vehicle->label,
                ['date' => $m->out_date->format('Y-m-d')], $m->in_date ? ['date' => $m->in_date->format('Y-m-d')] : 'Belum masuk',
                $m->driver_name ?: '-', $m->rental_name ?: '-', $m->booker_name ?: '-', (string) $m->notes];
        }
        $base = 'riwayat-keluar-masuk-'.\Illuminate\Support\Str::slug($vehicle->plate_number).'-'.now()->format('Ymd');
        $title = 'Riwayat Keluar / Masuk — '.$vehicle->plate_number.' · '.$vehicle->label
            .($from || $to ? ' ('.($from ?: '...').' s/d '.($to ?: '...').')' : '');

        $tmp = tempnam(sys_get_temp_dir(), 'xls');
        if (\App\Support\SimpleXlsx::build($tmp, 'Riwayat '.$vehicle->plate_number, $headers, $rows, [5, 14, 26, 16, 16, 24, 20, 24, 40], $title)) {
            return response()->download($tmp, $base.'.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
        }
        @unlink($tmp);
        $csv = "\xEF\xBB\xBF".implode(';', $headers)."\r\n";
        foreach ($rows as $row) {
            $row = array_map(fn ($c) => is_array($c) ? $c['date'] : (string) $c, $row);
            $csv .= implode(';', array_map(fn ($c) => '"'.str_replace('"', '""', $c).'"', $row))."\r\n";
        }
        return response($csv, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$base.'.csv"']);
    }

    private function logQuery(Vehicle $vehicle, ?string $from, ?string $to)
    {
        return \App\Models\VehicleLog::with('driver')->where('vehicle_id', $vehicle->id)
            ->when($from, fn ($x) => $x->whereDate('out_date', '>=', $from))
            ->when($to, fn ($x) => $x->whereDate('out_date', '<=', $to))
            ->orderByDesc('out_date')->orderByDesc('id');
    }

    private function dateOrNull($v): ?string
    {
        return is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : null;
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
