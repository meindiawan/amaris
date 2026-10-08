<?php
namespace App\Models;

use App\Support\Fmt;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = [];
    protected $casts = ['sim_expiry' => 'date', 'is_active' => 'boolean'];

    public const SIM_TYPES = ['A' => 'SIM A', 'B1' => 'SIM B1', 'B2' => 'SIM B2', 'B1U' => 'SIM B1 Umum', 'B2U' => 'SIM B2 Umum'];

    public function bookings() { return $this->hasMany(Booking::class); }

    public function getPhoneIntlAttribute(): string { return Fmt::phoneIntl($this->phone); }
    public function getWaPhoneAttribute(): string { return Fmt::waNumber($this->phone); }
    public function getSimLabelAttribute(): string
    {
        $t = self::SIM_TYPES[$this->sim_type] ?? $this->sim_type;
        return trim(($t ? $t.' · ' : '').($this->sim_number ?: '-'));
    }
    public function getSimStateAttribute(): ?array { return Fmt::expiry($this->sim_expiry); }

    /** Supir yang tidak dipakai booking lain (confirmed/ongoing) pada rentang waktu itu. */
    public function isBusy($start, $end, ?int $exceptBookingId = null): bool
    {
        return Booking::driverConflicts($this->id, $start, $end, $exceptBookingId)->exists();
    }
}
