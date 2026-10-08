<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Support\InvoicePdf;
use Illuminate\Http\Request;

class InvoicePublicController extends Controller
{
    private function find(string $token): Invoice
    {
        return Invoice::with(['items', 'booking.payments'])->where('token', $token)->where('is_published', true)->firstOrFail();
    }
    public function show(string $token) { return view('invoice-public', ['invoice' => $this->find($token)]); }
    public function pdf(Request $r, string $token) { return InvoicePdf::response($this->find($token), $r->boolean('download')); }

    /** Pelanggan membuka kembali booking dengan kode + 4 digit terakhir no. HP. */
    public function lookupForm() { return view('cek-booking'); }
    public function lookup(Request $r)
    {
        $d = $r->validate(['code' => 'required|max:20', 'phone' => 'required|max:30']);
        $b = Booking::where('code', strtoupper(trim($d['code'])))->first();
        $last = substr(preg_replace('/\D+/', '', $d['phone']), -4);
        if (!$b || strlen($last) < 4 || substr(preg_replace('/\D+/', '', $b->phone), -4) !== $last) {
            return back()->withInput()->withErrors(['code' => 'Kode booking atau nomor HP tidak cocok.']);
        }
        return redirect()->route('booking.success', $b->code);
    }
}
