<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMovement extends Model
{
    protected $guarded = [];
    protected $casts = ['moved_at' => 'datetime'];

    public const TYPES = ['keluar' => 'Keluar (jalan)', 'masuk' => 'Masuk (kembali)'];

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function driver() { return $this->belongsTo(Driver::class); }
    public function getTypeLabelAttribute(): string { return self::TYPES[$this->type] ?? $this->type; }
}
