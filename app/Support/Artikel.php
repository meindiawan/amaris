<?php
namespace App\Support;

/**
 * Daftar artikel halaman publik. Konten ditulis langsung di kode (resources/views/artikel/isi/*.blade.php),
 * jadi tidak butuh tabel database / migration baru.
 */
class Artikel
{
    public static function all(): array
    {
        return [
            'rental-mobil-harian-semarang' => [
                'title' => 'Sewa dan Rental Mobil Harian Semarang Terbaik untuk Bisnis serta Wisata',
                'excerpt' => 'Mendapatkan transportasi selaras dengan jadwal perjalanan di Ibu Kota Jawa Tengah kini semakin mudah. Layanan rental mobil harian Semarang menyajikan kebebasan mobilitas bagi kegiatan bisnis maupun liburan keluarga. Amaris Rent Car menyediakan pilihan armada terawat, tarif transparan, serta pengemudi profesional yang mengenal rute jalan dengan baik.',
                'image' => 'images/artikel/artikel-1.jpg',
                'view' => 'artikel.isi.semarang',
            ],
            'rental-mobil-murah-terdekat' => [
                'title' => 'Solusi rental mobil murah terdekat: Pilihan Cerdas untuk Perjalanan Anda bersama Amaris Rent Car',
                'excerpt' => 'Mencari rental mobil murah terdekat tidak lagi sulit. Amaris Rent Car menyediakan beragam kendaraan berkualitas dengan penawaran harga yang kompetitif dan proses yang mudah. Temukan fleksibilitas dan kenyamanan dalam setiap perjalanan, mulai dari kebutuhan pribadi hingga operasional, memastikan Anda mendapatkan layanan terbaik tanpa perlu kerumitan.',
                'image' => 'images/artikel/artikel-2.jpg',
                'view' => 'artikel.isi.murah-terdekat',
            ],
            'rental-mobil-murah-surabaya' => [
                'title' => 'Solusi Transportasi Efisien: Memilih Rental Mobil Murah Surabaya yang Tepat',
                'excerpt' => 'Mencari Rental Mobil Murah Surabaya kini lebih mudah. Artikel ini memandu Anda menemukan penyedia mobil sewa dengan harga terjangkau dan layanan berkualitas, memastikan perjalanan Anda di Surabaya lancar tanpa kendala.',
                'image' => 'images/artikel/artikel-3.jpg',
                'view' => 'artikel.isi.surabaya',
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        $all = self::all();
        return isset($all[$slug]) ? $all[$slug] + ['slug' => $slug] : null;
    }
}
