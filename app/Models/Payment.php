<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];
    protected $casts = ['paid_at' => 'datetime', 'amount' => 'integer'];

    public const TYPES = ['dp' => 'DP (uang muka)', 'cicilan' => 'Cicilan', 'pelunasan' => 'Pelunasan', 'deposit' => 'Deposit / jaminan'];
    public const METHODS = ['transfer' => 'Transfer bank', 'tunai' => 'Tunai', 'qris' => 'QRIS / e-wallet', 'lainnya' => 'Lainnya'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function getTypeLabelAttribute(): string { return self::TYPES[$this->type] ?? $this->type; }
    public function getMethodLabelAttribute(): string { return self::METHODS[$this->method] ?? $this->method; }
}
