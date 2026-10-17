<?php
namespace App\Support;

/**
 * Daftar harga sewa yang tampil di landing page. Ditulis langsung di kode (tanpa tabel/migration baru).
 * Ubah angka di sini; harga null = tampil "Kontak Kami".
 */
class Harga
{
    public const PAKET = 'Per Hari All In (Unit, Driver, BBM)';

    public static function all(): array
    {
        return [
            ['unit' => 'Alphard Facelift',    'harga' => null],
            ['unit' => 'Alphard Transformer', 'harga' => null],
            ['unit' => 'Rubicon 4×4',         'harga' => 4500000],
            ['unit' => 'Vellfire Facelift',   'harga' => 3500000],
            ['unit' => 'Fortuner 4X4',        'harga' => 3000000],
            ['unit' => 'Pajero Sport Dakar',  'harga' => 1800000],
            ['unit' => 'Camry',               'harga' => 1700000],
            ['unit' => 'Fortuner VRZ TRD',    'harga' => 1450000],
            ['unit' => 'Hiace Premio',        'harga' => 1600000],
            ['unit' => 'Toyota Innova Reborn','harga' => 750000],
            ['unit' => 'Expander',            'harga' => 600000],
            ['unit' => 'New Avanza CVT',      'harga' => 600000],
        ];
    }
}
