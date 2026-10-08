<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->string('code', 20)->unique();
            $t->foreignId('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $t->string('car_name');
            $t->string('service_type', 20);              // self_drive | with_driver | all_in
            $t->dateTime('start_at');
            $t->dateTime('end_at');
            $t->unsignedSmallInteger('days')->default(1);
            $t->string('pickup_method', 20)->default('office');   // office | delivery
            $t->text('pickup_address')->nullable();
            $t->string('return_method', 20)->default('office');   // office | pickup
            $t->text('return_address')->nullable();
            $t->string('customer_name');
            $t->string('phone', 30);
            $t->string('email')->nullable();
            $t->string('nik', 20);
            $t->text('address');
            $t->string('sim_number', 40)->nullable();
            $t->string('destination')->nullable();
            $t->unsignedSmallInteger('passengers')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedInteger('price_per_day')->default(0);
            $t->unsignedInteger('service_fee_per_day')->default(0);
            $t->unsignedInteger('delivery_fee')->default(0);
            $t->unsignedBigInteger('total_estimate')->default(0);
            $t->string('status', 20)->default('pending'); // pending|confirmed|ongoing|completed|cancelled
            $t->text('admin_notes')->nullable();
            $t->timestamps();
            $t->index(['car_id', 'status', 'start_at', 'end_at']);
        });

        // Default biaya layanan (bisa diubah dari Admin → Pengaturan)
        foreach (['driver_fee' => '150000', 'allin_fee' => '350000', 'delivery_fee' => '0'] as $k => $v) {
            if (!DB::table('site_settings')->where('key', $k)->exists()) {
                DB::table('site_settings')->insert(['key' => $k, 'value' => $v, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Teks FAQ lama masih menyebut booking via WhatsApp saja — sesuaikan hanya jika belum diedit admin.
        DB::table('faqs')->where('answer', 'Pilih mobil yang Anda inginkan lalu klik tombol WhatsApp. Admin akan membantu konfirmasi detail penyewaan.')
            ->update(['answer' => 'Pilih mobil, klik "Booking", lalu isi formulir (paket sewa, tanggal, lokasi, dan data diri). Admin akan menghubungi Anda via WhatsApp untuk konfirmasi.']);
        DB::table('faqs')->where('answer', 'Harga katalog adalah harga sewa unit. Kebutuhan sopir atau layanan tambahan dapat dikonfirmasi langsung ke admin.')
            ->update(['answer' => 'Harga katalog adalah harga sewa unit. Pada formulir booking Anda bisa memilih Mobil Saja, Mobil + Supir, atau All In (supir + BBM dll); estimasi biayanya tampil otomatis.']);
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
