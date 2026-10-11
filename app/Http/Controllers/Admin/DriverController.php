<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Support\Fmt;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));
        $drivers = Driver::when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")))
            ->orderByDesc('is_active')->orderBy('name')->get();
        $busy = Booking::whereIn('status', Booking::BLOCKING)->whereNotNull('driver_id')->where('end_at', '>=', now())->get()->groupBy('driver_id');
        return view('admin.drivers.index', compact('drivers', 'q', 'busy'));
    }
    public function create() { return view('admin.drivers.form', ['driver' => new Driver(['is_active' => true])]); }
    public function store(Request $r) { Driver::create($this->data($r)); return redirect()->route('admin.drivers.index')->with('ok', 'Supir ditambahkan.'); }
    public function edit(Driver $driver) { return view('admin.drivers.form', compact('driver')); }
    public function update(Request $r, Driver $driver) { $driver->update($this->data($r)); return redirect()->route('admin.drivers.index')->with('ok', 'Data supir diperbarui.'); }
    public function destroy(Driver $driver) { $driver->delete(); return back()->with('ok', 'Supir dihapus.'); }

    /** Form supir hanya: nama, no. WA, foto SIM. */
    private function data(Request $r): array
    {
        $d = $r->validate([
            'name' => 'required|max:120', 'phone' => 'required|max:30',
            'sim_photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
        ], ['name.required' => 'Nama supir wajib diisi.', 'phone.required' => 'No. WA supir wajib diisi.', 'sim_photo.mimes' => 'Foto SIM harus berupa JPG, PNG, WEBP atau PDF.']);
        $d['is_active'] = $r->boolean('is_active');
        unset($d['sim_photo']);
        if ($r->hasFile('sim_photo')) $d['sim_photo'] = Fmt::saveUpload($r->file('sim_photo'), 'sim');
        return $d;
    }
}
