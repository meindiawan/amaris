<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = [];
    protected $casts = ['amount' => 'integer', 'price' => 'integer', 'qty' => 'integer', 'days' => 'integer'];

    public function invoice() { return $this->belongsTo(Invoice::class); }

    private static function lines(?string $t): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $t)), fn ($l) => $l !== ''));
    }
    public function mobilLines(): array { return self::lines($this->mobil_text); }
    public function ruteLines(): array { return self::lines($this->rute_text); }
}
