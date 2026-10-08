<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Operasional rental: supir, unit kendaraan (plat, STNK, barcode), pembayaran,
 * setoran, mobil keluar-masuk, serta kolom penugasan pada tabel bookings.
 * Semua tabel/kolom lama TIDAK diubah — hanya menambah.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('phone', 30)->nullable();
            $t->string('emergency_phone', 30)->nullable();
            $t->text('address')->nullable();
            $t->string('nik', 20)->nullable();
            $t->string('sim_number', 40)->nullable();
            $t->string('sim_type', 20)->nullable();
            $t->date('sim_expiry')->nullable();
            $t->string('photo')->nullable();
            $t->boolean('is_active')->default(true);
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $t->string('plate_number', 20)->unique();
            $t->string('display_name', 120)->nullable();      // nama di invoice / booking order (default: nama mobil katalog)
            $t->unsignedSmallInteger('year')->nullable();
            $t->string('color', 40)->nullable();
            $t->string('chassis_number', 40)->nullable();
            $t->string('engine_number', 40)->nullable();
            $t->string('stnk_number', 40)->nullable();
            $t->string('stnk_owner', 120)->nullable();
            $t->date('stnk_expiry')->nullable();               // masa berlaku STNK (5 tahunan)
            $t->date('tax_expiry')->nullable();                // jatuh tempo pajak tahunan
            $t->string('stnk_file')->nullable();               // scan / foto STNK
            $t->string('barcode', 30)->unique();               // kode barcode unit (Code128)
            $t->string('owner_type', 10)->default('own');      // own | partner
            $t->string('owner_name', 120)->nullable();
            $t->string('owner_phone', 30)->nullable();
            $t->unsignedInteger('setoran_per_day')->default(0);
            $t->unsignedInteger('odometer')->nullable();
            $t->boolean('is_out')->default(false);             // sedang di luar garasi?
            $t->dateTime('last_moved_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::table('bookings', function (Blueprint $t) {
            $t->unsignedBigInteger('vehicle_id')->nullable()->index();
            $t->unsignedBigInteger('driver_id')->nullable()->index();
            $t->string('order_name', 150)->nullable();         // "Booking order : ..."
            $t->string('pelayanan', 150)->nullable();
            $t->string('giat', 150)->nullable();
            $t->string('standby_location', 200)->nullable();
            $t->string('standby_time', 30)->nullable();
            $t->string('guest_name', 120)->nullable();
            $t->string('guest_phone', 30)->nullable();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $t->string('type', 20)->default('dp');             // dp | cicilan | pelunasan | deposit
            $t->unsignedBigInteger('amount');
            $t->string('method', 30)->default('transfer');     // transfer | tunai | qris | lainnya
            $t->dateTime('paid_at');
            $t->string('reference', 150)->nullable();
            $t->string('proof')->nullable();
            $t->string('received_by', 80)->nullable();
            $t->timestamps();
            $t->index(['booking_id', 'paid_at']);
        });

        Schema::create('setorans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $t->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $t->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $t->string('kind', 20)->default('unit');           // unit | supir | lainnya
            $t->unsignedBigInteger('amount');
            $t->date('paid_at');
            $t->string('method', 30)->default('tunai');
            $t->text('note')->nullable();
            $t->string('received_by', 80)->nullable();
            $t->timestamps();
            $t->index('paid_at');
        });

        Schema::create('vehicle_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $t->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $t->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $t->string('type', 10);                            // keluar | masuk
            $t->dateTime('moved_at');
            $t->unsignedInteger('odometer')->nullable();
            $t->string('fuel', 10)->nullable();
            $t->string('destination', 200)->nullable();
            $t->text('condition_notes')->nullable();
            $t->string('recorded_by', 80)->nullable();
            $t->timestamps();
            $t->index(['vehicle_id', 'moved_at']);
        });

        $defaults = [
            'bank_name' => 'BCA', 'bank_account' => '4620 707070', 'bank_holder' => 'Damaris Eka Wijayanti',
            'invoice_signer' => 'Ardiansyah Kemas', 'invoice_prefix' => 'ARC/USR', 'invoice_note' => '',
            'site_url' => 'Http://amarisrentcar.com',
        ];
        foreach ($defaults as $k => $v) {
            if (!DB::table('site_settings')->where('key', $k)->exists()) {
                DB::table('site_settings')->insert(['key' => $k, 'value' => $v, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_movements');
        Schema::dropIfExists('setorans');
        Schema::dropIfExists('payments');
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropColumn(['vehicle_id', 'driver_id', 'order_name', 'pelayanan', 'giat', 'standby_location', 'standby_time', 'guest_name', 'guest_phone']);
        });
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
    }
};
