<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Catatan mobil keluar / masuk — satu baris = satu perjalanan. */
class VehicleLog extends Model
{
    protected $guarded = [];
    protected $casts = ['out_date' => 'date', 'in_date' => 'date'];

    public const INVOICE = ['belum_dibuat' => 'Belum dibuat', 'sudah_dibuat' => 'Sudah dibuat', 'sudah_terkirim' => 'Sudah terkirim'];
    public const PAYMENT = ['belum' => 'Belum payment', 'sudah' => 'Sudah payment'];

    protected static function booted(): void
    {
        // Pengaman terakhir: tanggal masuk tidak boleh lebih awal dari tanggal keluar, dari form mana pun.
        static::saving(function (self $l) {
            if ($l->in_date && $l->out_date && $l->in_date->lt($l->out_date)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['in_date' => 'Tanggal masuk tidak boleh sebelum tanggal keluar.']);
            }
        });
        static::saved(function (self $l) {
            $l->syncVehicle($l->vehicle_id);
            if ($l->wasChanged('vehicle_id') && $l->getOriginal('vehicle_id')) $l->syncVehicle($l->getOriginal('vehicle_id'));
        });
        static::deleted(fn (self $l) => $l->syncVehicle($l->vehicle_id));
    }

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function driver() { return $this->belongsTo(Driver::class); }

    public function getPlateNbAttribute(): string { return str_replace(' ', "\u{00A0}", (string) $this->plate_number); }

    public function getIsOpenAttribute(): bool { return $this->in_date === null; }

    /** Samakan penanda "sedang keluar" pada unit dengan catatan terbaru. */
    public function syncVehicle($vehicleId): void
    {
        if (!$vehicleId || !($v = Vehicle::find($vehicleId))) return;
        $open = self::where('vehicle_id', $v->id)->whereNull('in_date')->exists();
        $last = self::where('vehicle_id', $v->id)->orderByDesc('out_date')->orderByDesc('id')->first();
        $v->forceFill(['is_out' => $open, 'last_moved_at' => $last ? ($last->in_date ?? $last->out_date) : null])->saveQuietly();
    }
}
