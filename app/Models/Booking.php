<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $guarded = [];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public const SERVICES = [
        'self_drive'  => 'Mobil Saja (Lepas Kunci)',
        'with_driver' => 'Mobil + Supir',
        'all_in'      => 'All In (Supir + BBM dll)',
    ];
    /** Nama sementara untuk booking dari website (data penyewa tidak lagi diisi di formulir, diurus admin lewat WhatsApp). */
    public const WEB_NAME = 'Pelanggan Website';
    public const STATUSES = [
        'pending'   => 'Baru / Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'ongoing'   => 'Sedang Berjalan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    /** Status yang mengunci jadwal mobil. */
    public const BLOCKING = ['confirmed', 'ongoing'];
    /** Booking yang sudah memegang unit/supir (termasuk yang masih Baru/Menunggu) — dipakai untuk mencegah unit & supir bentrok. */
    public const ASSIGNING = ['pending', 'confirmed', 'ongoing'];

    public function car() { return $this->belongsTo(Car::class); }

    /** Booking yang berasal dari formulir website (bukan dibuat manual oleh admin). */
    public function getIsWebAttribute(): bool { return (int) $this->price_per_day > 0 || ($this->nik && $this->nik !== '-'); }

    public function getServiceLabelAttribute(): string { return self::SERVICES[$this->service_type] ?? $this->service_type; }
    public function getStatusLabelAttribute(): string { return self::STATUSES[$this->status] ?? $this->status; }
    public function getPickupLabelAttribute(): string { return $this->pickup_method === 'delivery' ? 'Diantar ke: '.$this->pickup_address : 'Ambil di kantor'; }
    public function getReturnLabelAttribute(): string { return $this->return_method === 'pickup' ? 'Dijemput di: '.$this->return_address : 'Kembalikan ke kantor'; }

    /** Nomor WhatsApp pelanggan dalam format internasional (62…). */
    public function getWaPhoneAttribute(): string
    {
        $n = preg_replace('/\D+/', '', $this->phone);
        if (Str::startsWith($n, '0')) return '62'.substr($n, 1);
        if (Str::startsWith($n, '8')) return '62'.$n;
        return $n;
    }

    public static function makeCode(): string
    {
        do { $code = 'AMR-'.strtoupper(Str::random(6)); } while (self::where('code', $code)->exists());
        return $code;
    }

    /** Booking lain (confirmed/ongoing) yang bentrok dengan rentang waktu tertentu. */
    public static function conflicts(int $carId, $start, $end, ?int $exceptId = null)
    {
        return self::where('car_id', $carId)
            ->whereIn('status', self::BLOCKING)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('start_at', '<', $end)->where('end_at', '>', $start);
    }

    // =====================================================================
    //  Operasional: penugasan unit & supir, pembayaran, invoice, booking order
    // =====================================================================
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function driver() { return $this->belongsTo(Driver::class); }
    public function payments() { return $this->hasMany(Payment::class)->orderBy('paid_at')->orderBy('id'); }
    public function invoice() { return $this->hasOne(Invoice::class)->latestOfMany(); }
    public function movements() { return $this->hasMany(VehicleMovement::class)->orderByDesc('moved_at'); }

    /** Booking lain (baru/confirmed/ongoing) yang memakai unit yang sama pada rentang waktu tertentu. */
    public static function vehicleConflicts(int $vehicleId, $start, $end, ?int $exceptId = null)
    {
        return self::where('vehicle_id', $vehicleId)->whereIn('status', self::ASSIGNING)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('start_at', '<', $end)->where('end_at', '>', $start);
    }
    public static function driverConflicts(int $driverId, $start, $end, ?int $exceptId = null)
    {
        return self::where('driver_id', $driverId)->whereIn('status', self::ASSIGNING)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('start_at', '<', $end)->where('end_at', '>', $start);
    }

    /** Sudah dibayar (DP + cicilan + pelunasan). Deposit jaminan tidak dihitung sebagai pembayaran sewa. */
    public function getPaidTotalAttribute(): int { return (int) $this->payments->where('type', '!=', 'deposit')->sum('amount'); }
    public function getDepositTotalAttribute(): int { return (int) $this->payments->where('type', 'deposit')->sum('amount'); }
    /** Tagihan: dari invoice bila sudah ada, kalau belum pakai estimasi booking. */
    public function getBillTotalAttribute(): int { return $this->invoice ? (int) $this->invoice->net : (int) $this->total_estimate; }
    public function getBalanceDueAttribute(): int { return max(0, $this->bill_total - $this->paid_total); }
    public function getPaymentStatusAttribute(): string
    {
        if ($this->paid_total <= 0) return 'unpaid';
        return $this->balance_due <= 0 ? 'paid' : 'partial';
    }
    public function getPaymentStatusLabelAttribute(): string
    {
        return ['paid' => 'Lunas', 'partial' => 'DP / sebagian', 'unpaid' => 'Belum dibayar'][$this->payment_status];
    }

    /** Unit sudah ditugaskan (dan supir bila paketnya memakai supir)? */
    public function getIsAssignedAttribute(): bool
    {
        if (!$this->vehicle_id) return false;
        return $this->service_type === 'self_drive' || (bool) $this->driver_id;
    }

    /** "27 - 28 April 2026", "30 Maret - 1 April 2026", "26 April 2026" */
    public function dateRangeLabel(): string
    {
        $s = $this->start_at; $e = $this->end_at;
        if ($s->isSameDay($e)) return $s->translatedFormat('j F Y');
        if ($s->year !== $e->year) return $s->translatedFormat('j F Y').' - '.$e->translatedFormat('j F Y');
        if ($s->month !== $e->month) return $s->translatedFormat('j F').' - '.$e->translatedFormat('j F Y');
        return $s->format('j').' - '.$e->translatedFormat('j F Y');
    }

    /** "Rabu - Kamis / 7 - 8 Okt 2026" (format surat Booking Order) */
    public function orderDateLabel(): string
    {
        $s = $this->start_at; $e = $this->end_at;
        if ($s->isSameDay($e)) return $s->translatedFormat('l').' / '.$s->translatedFormat('j M Y');
        $days = $s->translatedFormat('l').' - '.$e->translatedFormat('l');
        if ($s->year !== $e->year) return $days.' / '.$s->translatedFormat('j M Y').' - '.$e->translatedFormat('j M Y');
        if ($s->month !== $e->month) return $days.' / '.$s->translatedFormat('j M').' - '.$e->translatedFormat('j M Y');
        return $days.' / '.$s->format('j').' - '.$e->translatedFormat('j M Y');
    }

    /**
     * Teks "Booking Order" siap kirim via WhatsApp (format baku Amaris Rent Car).
     * Tersedia untuk pelanggan setelah unit & supir ditugaskan admin.
     */
    public function orderText(): string
    {
        $unit   = $this->vehicle?->label ?: $this->car_name;
        $nopol  = $this->vehicle?->plate_number ?: '-';
        $drv    = $this->driver?->name ?: ($this->service_type === 'self_drive' ? 'Tanpa supir (lepas kunci)' : '-');
        $drvTel = $this->driver ? $this->driver->phone_intl : '-';
        $url    = \App\Support\Fmt::setting('site_url', 'Http://amarisrentcar.com');
        $standby = $this->standby_location ?: ($this->pickup_method === 'delivery' ? $this->pickup_address : \App\Support\Fmt::setting('address', ''));

        return implode("\n", [
            'Booking order : '.($this->order_name ?: $this->customer_name),
            '',
            $this->orderDateLabel(),
            '',
            'Unit : '.$unit,
            'Nopol : '.$nopol,
            'Driver : '.$drv,
            'No hp : '.$drvTel,
            '',
            'Pelayanan : '.($this->pelayanan ?: ''),
            'Giat : '.($this->giat ?: ''),
            '',
            'Standby di : '.$standby,
            'Jam : '.($this->standby_time ?: $this->start_at->format('H.i').' WIB'),
            'Tujuan : '.($this->destination ?: ''),
            '',
            'Kontak person tamu',
            'Nama : '.($this->guest_name ?: ''),
            'Tlp : '.($this->guest_phone ?: ''),
            '',
            '*Terimakasih*',
            'sudah mempercayakan perjalanannya bersama *amarisrentcar*,',
            'PUSATnya rental mobil terpercaya seluruh indonesia',
            '',
            $url,
        ]);
    }
}
