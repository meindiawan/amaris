<?php
namespace App\Models;

use App\Support\Fmt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $guarded = [];
    protected $casts = ['issued_at' => 'date', 'is_published' => 'boolean', 'discount' => 'integer', 'paid_manual' => 'integer'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function items() { return $this->hasMany(InvoiceItem::class)->orderBy('sort')->orderBy('id'); }

    // ---------- angka ----------
    public function getSubtotalAttribute(): int { return (int) $this->items->sum('amount'); }
    public function getNetAttribute(): int { return max(0, $this->subtotal - (int) $this->discount); }
    /** Total yang sudah dibayar (DP + cicilan + pelunasan; deposit tidak dihitung). */
    public function getPaidAttribute(): int
    {
        return $this->booking ? (int) $this->booking->paid_total : (int) $this->paid_manual;
    }
    public function getDueAttribute(): int { return max(0, $this->net - $this->paid); }
    public function getPaidLabelAttribute(): string
    {
        if ($this->booking) {
            $pay = $this->booking->payments->where('type', '!=', 'deposit');
            if ($pay->count() === 1 && $pay->first()->type === 'dp') return 'DP';
        }
        return 'DIBAYAR';
    }
    public function getStatusAttribute(): string
    {
        if ($this->net <= 0) return 'paid';
        if ($this->paid <= 0) return 'unpaid';
        return $this->due <= 0 ? 'paid' : 'partial';
    }
    public function getStatusLabelAttribute(): string
    {
        return ['paid' => 'Lunas', 'partial' => 'Dibayar sebagian', 'unpaid' => 'Belum dibayar'][$this->status];
    }

    // ---------- tautan ----------
    public function publicUrl(): string { return route('invoice.public', $this->token); }
    public function publicPdfUrl(bool $download = false): string { return route('invoice.public.pdf', $this->token).($download ? '?download=1' : ''); }
    public function getFileNameAttribute(): string { return 'Invoice-'.str_replace('/', '-', $this->number).'.pdf'; }

    // ---------- penomoran ----------
    /** ARC/USR/10/001 — urutan berulang tiap bulan. */
    public static function nextNumber($date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();
        $prefix = trim((string) Fmt::setting('invoice_prefix', 'ARC/USR'), '/ ');
        $seq = self::whereYear('issued_at', $date->year)->whereMonth('issued_at', $date->month)->count() + 1;
        do {
            $no = $prefix.'/'.$date->format('m').'/'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            $seq++;
        } while (self::where('number', $no)->exists());
        return $no;
    }

    // ---------- pembuatan otomatis dari booking ----------
    public static function buildFromBooking(Booking $b): self
    {
        $b->loadMissing(['vehicle.car', 'driver', 'payments']);
        $inv = self::create([
            'number' => self::nextNumber(),
            'token' => Str::lower(Str::random(32)),
            'booking_id' => $b->id,
            'issued_at' => now()->toDateString(),
            'bill_to_name' => $b->order_name ?: $b->customer_name,
            'bill_to_city' => null,
            'bill_to_note' => $b->pelayanan ? '(Pelayanan '.$b->pelayanan.')' : null,
        ]);
        $inv->rebuildItems();
        return $inv;
    }

    /** Susun ulang baris invoice dari data booking (menghapus baris lama). */
    public function rebuildItems(): void
    {
        $b = $this->booking;
        if (!$b) return;
        $b->loadMissing(['vehicle.car', 'driver']);
        $this->items()->delete();

        $mobil = [strtoupper($b->vehicle?->label ?: $b->car_name)];
        if ($b->vehicle) $mobil[] = $b->vehicle->plate_number;
        $driver = $b->driver ? 'DRIVER '.strtoupper($b->driver->name) : null;
        switch ($b->service_type) {
            case 'self_drive': $mobil[] = 'UNIT ONLY'; break;
            case 'all_in':     $mobil[] = 'ALL IN'; if ($driver) $mobil[] = $driver; break;
            default:           $mobil[] = $driver ?: 'DENGAN SUPIR';
        }
        $rute = [$b->dateRangeLabel()];
        if ($b->destination) $rute[] = 'Tujuan : '.$b->destination;

        $price = (int) $b->price_per_day + (int) $b->service_fee_per_day;
        $sort = 1;
        $this->items()->create([
            'sort' => $sort++, 'kind' => 'main', 'mobil_text' => implode("\n", $mobil), 'rute_text' => implode("\n", $rute),
            'qty' => 1, 'days' => $b->days, 'price' => $price, 'amount' => $price * (int) $b->days,
        ]);
        if ((int) $b->delivery_fee > 0) {
            $this->items()->create(['sort' => $sort++, 'kind' => 'extra', 'rute_text' => 'Antar / jemput unit', 'amount' => (int) $b->delivery_fee]);
        }
        $this->unsetRelation('items');
    }
}
