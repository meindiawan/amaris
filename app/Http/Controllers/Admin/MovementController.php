<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $r)
    {
        $vehicles = Vehicle::with('car')->where('is_active', true)->orderBy('plate_number')->get();
        $logs = VehicleMovement::with(['vehicle', 'driver', 'booking'])
            ->when($r->filled('vehicle'), fn ($q) => $q->where('vehicle_id', $r->query('vehicle')))
            ->when($r->filled('date'), fn ($q) => $q->whereDate('moved_at', $r->query('date')))
            ->orderByDesc('moved_at')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.movements.index', compact('vehicles', 'logs'));
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
        $vehicle = Vehicle::with('car')->findOrFail($r->query('vehicle'));
        $type = $vehicle->is_out ? 'masuk' : 'keluar';
        $bookings = Booking::where('vehicle_id', $vehicle->id)->whereIn('status', ['confirmed', 'ongoing'])->orderBy('start_at')->get();
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();
        $lastOut = $vehicle->movements()->where('type', 'keluar')->first();
        return view('admin.movements.form', compact('vehicle', 'type', 'bookings', 'drivers', 'lastOut'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'vehicle_id' => 'required|exists:vehicles,id', 'type' => 'required|in:keluar,masuk',
            'moved_at' => 'required|date', 'booking_id' => 'nullable|exists:bookings,id', 'driver_id' => 'nullable|exists:drivers,id',
            'odometer' => 'nullable|integer|min:0', 'fuel' => 'nullable|in:E,1/4,1/2,3/4,F',
            'destination' => 'nullable|max:200', 'condition_notes' => 'nullable|max:2000',
        ]);
        $v = Vehicle::findOrFail($d['vehicle_id']);
        $d['recorded_by'] = auth()->user()->name;
        VehicleMovement::create($d);

        $v->is_out = $d['type'] === 'keluar';
        $v->last_moved_at = $d['moved_at'];
        if (!empty($d['odometer'])) $v->odometer = $d['odometer'];
        $v->save();

        if ($r->boolean('update_booking') && !empty($d['booking_id']) && ($b = Booking::find($d['booking_id']))) {
            if ($d['type'] === 'keluar' && $b->status === 'confirmed') $b->update(['status' => 'ongoing']);
            if ($d['type'] === 'masuk' && $b->status === 'ongoing') $b->update(['status' => 'completed']);
        }
        return redirect()->route('admin.movements.index')->with('ok', $v->plate_number.' tercatat '.strtoupper($d['type']).'.');
    }

    public function destroy(VehicleMovement $movement)
    {
        $v = $movement->vehicle;
        $movement->delete();
        if ($v) { $last = $v->movements()->first(); $v->update(['is_out' => $last && $last->type === 'keluar', 'last_moved_at' => $last?->moved_at]); }
        return back()->with('ok', 'Catatan dihapus.');
    }
}
