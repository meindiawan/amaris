<?php
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Revisi Oktober 2026 — hanya MENAMBAH (tidak ada tabel/kolom lama yang dihapus):
 *  - nomor WhatsApp admin baru
 *  - drivers.sim_photo (foto SIM supir)
 *  - vehicles.barcode_image (jaga-jaga bila migrasi sebelumnya belum jalan)
 *  - tabel vehicle_logs: catatan mobil keluar/masuk format baru (satu baris = satu perjalanan)
 *  - setting video dokumenter di landing page
 */
return new class extends Migration {
    public function up(): void
    {
        // 1) Nomor WA + video dokumenter
        $this->setting('whatsapp', '6285157024883', true);
        $this->setting('documentary_url', 'https://youtu.be/oOfvnOYGPVg', false);

        // 2) Foto SIM supir
        if (Schema::hasTable('drivers') && !Schema::hasColumn('drivers', 'sim_photo')) {
            Schema::table('drivers', fn (Blueprint $t) => $t->string('sim_photo')->nullable());
        }
        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'barcode_image')) {
            Schema::table('vehicles', fn (Blueprint $t) => $t->string('barcode_image')->nullable());
        }

        // 3) Mobil keluar / masuk (format baru)
        if (!Schema::hasTable('vehicle_logs')) {
            Schema::create('vehicle_logs', function (Blueprint $t) {
                $t->id();
                $t->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
                $t->string('plate_number', 20);
                $t->string('vehicle_type', 120)->nullable();
                $t->string('rental_name', 120)->nullable();
                $t->date('out_date');
                $t->date('in_date')->nullable();
                $t->string('booker_name', 150)->nullable();
                $t->string('invoice_status', 20)->default('belum_dibuat'); // belum_dibuat | sudah_dibuat | sudah_terkirim
                $t->string('payment_status', 20)->default('belum');        // belum | sudah
                $t->string('payment_note', 200)->nullable();               // siapa yang menerima payment
                $t->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
                $t->string('driver_name', 120)->nullable();
                $t->text('notes')->nullable();
                $t->string('recorded_by', 80)->nullable();
                $t->timestamps();
                $t->index('out_date');
                $t->index('plate_number');
            });
            $this->importOldMovements();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
        if (Schema::hasColumn('drivers', 'sim_photo')) {
            Schema::table('drivers', fn (Blueprint $t) => $t->dropColumn('sim_photo'));
        }
    }

    private function setting(string $key, string $value, bool $overwrite): void
    {
        if (!Schema::hasTable('site_settings')) return;
        $q = DB::table('site_settings')->where('key', $key);
        if ($q->exists()) {
            if ($overwrite) $q->update(['value' => $value, 'updated_at' => now()]);
        } else {
            DB::table('site_settings')->insert(['key' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /** Catatan keluar/masuk lama (vehicle_movements) dipasangkan jadi satu baris perjalanan. */
    private function importOldMovements(): void
    {
        if (!Schema::hasTable('vehicle_movements')) return;
        try {
            $vehicles = DB::table('vehicles')->get()->keyBy('id');
            $cars     = DB::table('cars')->pluck('name', 'id');
            $drivers  = DB::table('drivers')->pluck('name', 'id');
            $bookings = DB::table('bookings')->pluck('customer_name', 'id');
            $open = []; $rows = [];
            $moves = DB::table('vehicle_movements')->orderBy('vehicle_id')->orderBy('moved_at')->orderBy('id')->get();
            foreach ($moves as $m) {
                $v = $vehicles[$m->vehicle_id] ?? null;
                if (!$v) continue;
                $date = Carbon::parse($m->moved_at)->toDateString();
                if ($m->type === 'keluar') {
                    $rows[] = [
                        'vehicle_id' => $v->id, 'plate_number' => $v->plate_number,
                        'vehicle_type' => $v->display_name ?: ($cars[$v->car_id] ?? null), 'rental_name' => 'Amaris Rent Car',
                        'out_date' => $date, 'in_date' => null, 'booker_name' => $bookings[$m->booking_id] ?? null,
                        'invoice_status' => 'belum_dibuat', 'payment_status' => 'belum', 'payment_note' => null,
                        'driver_id' => $m->driver_id, 'driver_name' => $drivers[$m->driver_id] ?? null,
                        'notes' => trim(($m->destination ? 'Tujuan: '.$m->destination.'. ' : '').($m->condition_notes ?? '')) ?: null,
                        'recorded_by' => $m->recorded_by, 'created_at' => now(), 'updated_at' => now(),
                    ];
                    $open[$v->id] = array_key_last($rows);
                } elseif (isset($open[$v->id])) {
                    $rows[$open[$v->id]]['in_date'] = $date;
                    unset($open[$v->id]);
                } else {
                    $rows[] = [
                        'vehicle_id' => $v->id, 'plate_number' => $v->plate_number,
                        'vehicle_type' => $v->display_name ?: ($cars[$v->car_id] ?? null), 'rental_name' => 'Amaris Rent Car',
                        'out_date' => $date, 'in_date' => $date, 'booker_name' => $bookings[$m->booking_id] ?? null,
                        'invoice_status' => 'belum_dibuat', 'payment_status' => 'belum', 'payment_note' => null,
                        'driver_id' => $m->driver_id, 'driver_name' => $drivers[$m->driver_id] ?? null,
                        'notes' => $m->condition_notes, 'recorded_by' => $m->recorded_by, 'created_at' => now(), 'updated_at' => now(),
                    ];
                }
            }
            foreach (array_chunk($rows, 100) as $chunk) DB::table('vehicle_logs')->insert($chunk);
        } catch (\Throwable $e) {
            // Impor data lama bersifat opsional — kegagalan tidak boleh menggagalkan migrasi.
        }
    }
};
