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
        $drivers = Driver::when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->orWhere('sim_number', 'like', "%$q%")))
            ->orderByDesc('is_active')->orderBy('name')->get();
        $busy = Booking::whereIn('status', Booking::BLOCKING)->whereNotNull('driver_id')->where('end_at', '>=', now())->get()->groupBy('driver_id');
        return view('admin.drivers.index', compact('drivers', 'q', 'busy'));
    }
    public function create() { return view('admin.drivers.form', ['driver' => new Driver(['is_active' => true])]); }
    public function store(Request $r) { Driver::create($this->data($r)); return redirect()->route('admin.drivers.index')->with('ok', 'Supir ditambahkan.'); }
    public function edit(Driver $driver) { return view('admin.drivers.form', compact('driver')); }
    public function update(Request $r, Driver $driver) { $driver->update($this->data($r)); return redirect()->route('admin.drivers.index')->with('ok', 'Data supir diperbarui.'); }
    public function destroy(Driver $driver) { $driver->delete(); return back()->with('ok', 'Supir dihapus.'); }

    private function data(Request $r): array
    {
        $d = $r->validate([
            'name' => 'required|max:120', 'phone' => 'required|max:30', 'emergency_phone' => 'nullable|max:30',
            'address' => 'nullable|max:500', 'nik' => 'nullable|max:20', 'sim_number' => 'nullable|max:40',
            'sim_type' => 'nullable|max:20', 'sim_expiry' => 'nullable|date', 'notes' => 'nullable|max:2000',
            'photo' => 'nullable|image|max:4096', 'sim_photo' => 'nullable|image|max:4096',
        ], ['name.required' => 'Nama supir wajib diisi.', 'phone.required' => 'No. HP supir wajib diisi.']);
        $d['is_active'] = $r->has('is_active') ? $r->boolean('is_active') : true;
        unset($d['photo'], $d['sim_photo']);
        if ($r->hasFile('photo')) $d['photo'] = Fmt::saveUpload($r->file('photo'), 'drivers');
        if ($r->hasFile('sim_photo')) $d['sim_photo'] = Fmt::saveUpload($r->file('sim_photo'), 'sim');
        return $d;
    }
}
