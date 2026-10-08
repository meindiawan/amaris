<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Support\Fmt;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $payments = Payment::with('booking')
            ->when($r->filled('from'), fn ($q) => $q->whereDate('paid_at', '>=', $r->query('from')))
            ->when($r->filled('to'), fn ($q) => $q->whereDate('paid_at', '<=', $r->query('to')))
            ->when($r->filled('q'), fn ($q) => $q->whereHas('booking', fn ($b) => $b->where('code', 'like', '%'.$r->query('q').'%')->orWhere('customer_name', 'like', '%'.$r->query('q').'%')))
            ->orderByDesc('paid_at')->orderByDesc('id')->paginate(20)->withQueryString();
        $total = Payment::where('type', '!=', 'deposit')
            ->when($r->filled('from'), fn ($q) => $q->whereDate('paid_at', '>=', $r->query('from')))
            ->when($r->filled('to'), fn ($q) => $q->whereDate('paid_at', '<=', $r->query('to')))->sum('amount');
        return view('admin.payments.index', compact('payments', 'total'));
    }

    public function create(Request $r)
    {
        $bookings = Booking::whereNotIn('status', ['cancelled'])->latest()->take(100)->get();
        return view('admin.payments.form', ['bookings' => $bookings, 'bookingId' => $r->query('booking')]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'booking_id' => 'required|exists:bookings,id', 'type' => 'required|in:dp,cicilan,pelunasan,deposit',
            'amount' => 'required|integer|min:1', 'method' => 'required|in:transfer,tunai,qris,lainnya',
            'paid_at' => 'required|date', 'reference' => 'nullable|max:150', 'proof' => 'nullable|image|max:4096',
        ], ['amount.required' => 'Jumlah pembayaran wajib diisi.', 'amount.min' => 'Jumlah harus lebih dari 0.']);
        unset($d['proof']);
        if ($r->hasFile('proof')) $d['proof'] = Fmt::saveUpload($r->file('proof'), 'payments');
        $d['received_by'] = auth()->user()->name;
        $p = Payment::create($d);
        return redirect()->route('admin.bookings.show', $p->booking_id)->with('ok', 'Pembayaran '.Fmt::rp($p->amount).' dicatat.');
    }

    public function destroy(Payment $payment) { $payment->delete(); return back()->with('ok', 'Pembayaran dihapus.'); }
}
