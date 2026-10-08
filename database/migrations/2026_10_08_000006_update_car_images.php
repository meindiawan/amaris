<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mengganti foto contoh (Unsplash, tidak sesuai nama mobil) dengan foto asli tiap model
 * dari Wikimedia Commons. Hanya mobil yang masih memakai foto contoh yang diganti,
 * foto yang sudah diupload admin tidak disentuh.
 */
return new class extends Migration {
    public function up(): void
    {
        $images = [
            'toyota-innova-reborn' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Toyota_Innova_Reborn_2022.jpg?width=1200',
            'toyota-fortuner' => 'https://commons.wikimedia.org/wiki/Special:FilePath/2021_Toyota_Fortuner_2.4_VRZ_TRD_Sportivo_%28front%29%2C_Pakuwon_Mall%2C_West_Surabaya.jpg?width=1200',
            'honda-brio' => 'https://commons.wikimedia.org/wiki/Special:FilePath/2023_Honda_Brio_Satya_E_%28facelift%29.jpg?width=1200',
            'toyota-avanza' => 'https://commons.wikimedia.org/wiki/Special:FilePath/2022_Toyota_Avanza_%28W100%29_1.5_G%2C_Pakuwon_Trade_Center%2C_West_Surabaya.jpg?width=1200',
        ];
        foreach ($images as $slug => $url) {
            DB::table('cars')->where('slug', $slug)->where('image', 'like', '%images.unsplash.com%')->update(['image' => $url, 'updated_at' => now()]);
        }
    }

    public function down(): void {}
};
