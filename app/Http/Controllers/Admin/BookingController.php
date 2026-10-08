<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->query('status');
        $q = trim((string) $r->query('q'));
        $bookings = Booking::query()
            ->when(isset(Booking::STATUSES[$status]), fn ($x) => $x->where('status', $status))
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w
                ->where('customer_name', 'like', "%$q%")->orWhere('code', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")->orWhere('car_name', 'like', "%$q%")))
            ->latest()->paginate(15)->withQueryString();
        $counts = Booking::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        return view('admin.bookings.index', compact('bookings', 'counts', 'status', 'q'));
    }

    public function show(Booking $booking)
    {
        $conflict = $booking->car_id && !in_array($booking->status, Booking::BLOCKING)
            ? Booking::conflicts($booking->car_id, $booking->start_at, $booking->end_at, $booking->id)->first() : null;
        return view('admin.bookings.show', compact('booking', 'conflict'));
    }

    public function status(Request $r, Booking $booking)
    {
        $d = $r->validate(['status' => 'required|in:'.implode(',', array_keys(Booking::STATUSES)), 'admin_notes' => 'nullable|string|max:2000']);
        if (in_array($d['status'], Booking::BLOCKING) && $booking->car_id
            && Booking::conflicts($booking->car_id, $booking->start_at, $booking->end_at, $booking->id)->exists()) {
            return back()->withErrors(['status' => 'Tidak bisa dikonfirmasi: jadwal bentrok dengan booking lain yang sudah dikonfirmasi untuk mobil ini.']);
        }
        $booking->update($d);
        return back()->with('ok', 'Booking '.$booking->code.' diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('ok', 'Booking dihapus.');
    }

    /** Penugasan unit + supir + data Booking Order. */
    public function assign(Request $r, Booking $booking)
    {
        $d = $r->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id', 'driver_id' => 'nullable|exists:drivers,id',
            'order_name' => 'nullable|max:150', 'pelayanan' => 'nullable|max:150', 'giat' => 'nullable|max:150',
            'standby_location' => 'nullable|max:200', 'standby_time' => 'nullable|max:30', 'destination' => 'nullable|max:200',
            'guest_name' => 'nullable|max:120', 'guest_phone' => 'nullable|max:30',
        ]);
        $active = in_array($booking->status, Booking::BLOCKING) || $booking->status === 'pending';
        if ($active && !empty($d['vehicle_id']) && Booking::vehicleConflicts((int) $d['vehicle_id'], $booking->start_at, $booking->end_at, $booking->id)->exists()) {
            return back()->withInput()->withErrors(['vehicle_id' => 'Unit ini sudah dipakai booking lain (dikonfirmasi) pada tanggal tersebut.']);
        }
        if ($active && !empty($d['driver_id']) && Booking::driverConflicts((int) $d['driver_id'], $booking->start_at, $booking->end_at, $booking->id)->exists()) {
            return back()->withInput()->withErrors(['driver_id' => 'Supir ini sudah bertugas di booking lain pada tanggal tersebut.']);
        }
        foreach (['vehicle_id', 'driver_id'] as $k) $d[$k] = $d[$k] ?? null;
        $booking->update($d);
        return back()->with('ok', 'Penugasan & Booking Order disimpan.');
    }
}
