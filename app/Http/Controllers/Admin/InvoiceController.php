<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Support\InvoicePdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q'));
        $invoices = Invoice::with(['items', 'booking.payments'])
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('number', 'like', "%$q%")->orWhere('bill_to_name', 'like', "%$q%")))
            ->orderByDesc('issued_at')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.invoices.index', compact('invoices', 'q'));
    }

    /** Buat invoice otomatis dari booking (atau buka invoice yang sudah ada). */
    public function fromBooking(Booking $booking)
    {
        if ($inv = $booking->invoice) return redirect()->route('admin.invoices.show', $inv);
        $inv = Invoice::buildFromBooking($booking);
        return redirect()->route('admin.invoices.show', $inv)->with('ok', 'Invoice '.$inv->number.' dibuat otomatis dari booking. Periksa lalu klik "Kirim ke pelanggan".');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'booking.payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice) { $invoice->load('items'); return view('admin.invoices.edit', compact('invoice')); }

    public function update(Request $r, Invoice $invoice)
    {
        $d = $r->validate([
            'number' => 'required|max:40|unique:invoices,number,'.$invoice->id, 'issued_at' => 'required|date',
            'bill_to_name' => 'required|max:150', 'bill_to_city' => 'nullable|max:100', 'bill_to_note' => 'nullable|max:200',
            'discount_label' => 'nullable|max:40', 'discount' => 'nullable|integer|min:0', 'paid_manual' => 'nullable|integer|min:0', 'notes' => 'nullable|max:1000',
            'items' => 'array', 'items.*.kind' => 'in:main,extra', 'items.*.mobil_text' => 'nullable|max:500', 'items.*.rute_text' => 'nullable|max:500',
            'items.*.qty' => 'nullable|integer|min:0', 'items.*.days' => 'nullable|integer|min:0',
            'items.*.price' => 'nullable|integer|min:0', 'items.*.amount' => 'nullable|integer|min:0',
        ]);
        $items = $d['items'] ?? [];
        unset($d['items']);
        $d['discount'] = (int) ($d['discount'] ?? 0);
        $d['paid_manual'] = (int) ($d['paid_manual'] ?? $invoice->paid_manual);
        $d['discount_label'] = ($d['discount_label'] ?? '') ?: 'DISKON';
        $invoice->update($d);

        $invoice->items()->delete();
        $sort = 1;
        foreach ($items as $it) {
            $empty = trim(($it['mobil_text'] ?? '').($it['rute_text'] ?? '')) === '' && empty($it['amount']) && empty($it['price']);
            if ($empty) continue;
            $price = ($it['price'] ?? '') === '' ? null : (int) $it['price'];
            $days = ($it['days'] ?? '') === '' ? null : (int) $it['days'];
            $qty = ($it['qty'] ?? '') === '' ? null : (int) $it['qty'];
            $amount = ($it['amount'] ?? '') === '' ? ($price !== null ? $price * ($days ?? 1) : 0) : (int) $it['amount'];
            $invoice->items()->create([
                'sort' => $sort++, 'kind' => $it['kind'], 'mobil_text' => $it['mobil_text'] ?? null, 'rute_text' => $it['rute_text'] ?? null,
                'qty' => $qty, 'days' => $days, 'price' => $price, 'amount' => $amount,
            ]);
        }
        return redirect()->route('admin.invoices.show', $invoice)->with('ok', 'Invoice disimpan.');
    }

    public function destroy(Invoice $invoice) { $invoice->delete(); return redirect()->route('admin.invoices.index')->with('ok', 'Invoice dihapus.'); }

    public function pdf(Request $r, Invoice $invoice) { return InvoicePdf::response($invoice, $r->boolean('download')); }

    public function publish(Invoice $invoice)
    {
        $invoice->update(['is_published' => !$invoice->is_published]);
        return back()->with('ok', $invoice->is_published ? 'Invoice sekarang bisa dilihat & diunduh pelanggan.' : 'Invoice disembunyikan dari pelanggan.');
    }

    public function rebuild(Invoice $invoice)
    {
        if (!$invoice->booking) return back()->withErrors(['invoice' => 'Invoice ini tidak terhubung ke booking.']);
        $invoice->rebuildItems();
        return back()->with('ok', 'Baris invoice disusun ulang dari data booking.');
    }
}
