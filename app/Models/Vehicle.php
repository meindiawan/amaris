<?php
namespace App\Models;

use App\Support\Code128;
use App\Support\Fmt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    protected $guarded = [];
    protected $casts = [
        'stnk_expiry' => 'date', 'tax_expiry' => 'date', 'last_moved_at' => 'datetime',
        'is_out' => 'boolean', 'is_active' => 'boolean',
    ];

    public const OWNER_TYPES = ['own' => 'Milik sendiri', 'partner' => 'Titipan / mitra'];
    public const FUEL = ['E' => 'Kosong (E)', '1/4' => '1/4', '1/2' => '1/2', '3/4' => '3/4', 'F' => 'Penuh (F)'];

    protected static function booted(): void
    {
        static::creating(function (self $v) {
            $v->barcode = $v->barcode ?: 'TMP'.Str::upper(Str::random(10));
            $v->plate_number = self::normalizePlate($v->plate_number);
        });
        static::updating(function (self $v) { $v->plate_number = self::normalizePlate($v->plate_number); });
        static::created(function (self $v) {
            if (str_starts_with($v->barcode, 'TMP')) {
                $v->forceFill(['barcode' => 'AMR-'.str_pad((string) $v->id, 4, '0', STR_PAD_LEFT)])->saveQuietly();
            }
        });
    }

    /** "h1748un" → "H 1748 UN" */
    public static function normalizePlate(?string $p): string
    {
        $p = strtoupper(trim((string) $p));
        if (preg_match('/^([A-Z]{1,2})\s*(\d{1,4})\s*([A-Z]{0,3})$/', $p, $m)) return trim($m[1].' '.$m[2].' '.$m[3]);
        return preg_replace('/\s+/', ' ', $p);
    }

    public function car() { return $this->belongsTo(Car::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function movements() { return $this->hasMany(VehicleMovement::class)->orderByDesc('moved_at')->orderByDesc('id'); }
    public function setorans() { return $this->hasMany(Setoran::class); }

    public function getLabelAttribute(): string { return $this->display_name ?: ($this->car->name ?? 'Unit'); }
    public function getInvoiceNameAttribute(): string { return strtoupper($this->label); }
    public function getBarcodeSvgAttribute(): string { return Code128::svg($this->barcode, 70, 2); }
    public function getStnkStateAttribute(): ?array { return Fmt::expiry($this->stnk_expiry); }
    public function getTaxStateAttribute(): ?array { return Fmt::expiry($this->tax_expiry); }

    /** Cari unit dari hasil scan: kode barcode atau plat nomor. */
    public static function findByScan(string $code): ?self
    {
        $code = strtoupper(trim($code));
        if ($code === '') return null;
        if ($v = self::where('barcode', $code)->first()) return $v;
        $norm = fn ($s) => preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $s));
        $want = $norm($code);
        return self::all()->first(fn ($v) => $norm($v->plate_number) === $want);
    }

    /** Unit sedang dipakai booking lain (confirmed/ongoing) pada rentang waktu tsb? */
    public function isBusy($start, $end, ?int $exceptBookingId = null): bool
    {
        return Booking::vehicleConflicts($this->id, $start, $end, $exceptBookingId)->exists();
    }
}
