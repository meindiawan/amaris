<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setorans';
    protected $guarded = [];
    protected $casts = ['paid_at' => 'date', 'amount' => 'integer'];

    public const KINDS = [
        'unit'    => 'Setoran unit (ke pemilik / mitra)',
        'supir'   => 'Setoran dari supir (ke kantor)',
        'lainnya' => 'Lainnya',
    ];
    public const METHODS = ['tunai' => 'Tunai', 'transfer' => 'Transfer bank', 'lainnya' => 'Lainnya'];

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function driver() { return $this->belongsTo(Driver::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function getKindLabelAttribute(): string { return self::KINDS[$this->kind] ?? $this->kind; }
    public function getMethodLabelAttribute(): string { return self::METHODS[$this->method] ?? $this->method; }
}
