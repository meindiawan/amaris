<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->string('number', 40)->unique();               // ARC/USR/10/001
            $t->string('token', 40)->unique();                // tautan publik untuk pelanggan
            $t->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $t->date('issued_at');
            $t->string('bill_to_name', 150);
            $t->string('bill_to_city', 100)->nullable();
            $t->string('bill_to_note', 200)->nullable();      // contoh: (Pelayanan BJP Rudi)
            $t->string('discount_label', 40)->default('DISKON');
            $t->unsignedBigInteger('discount')->default(0);
            $t->unsignedBigInteger('paid_manual')->default(0); // dipakai bila invoice tanpa booking
            $t->text('notes')->nullable();
            $t->boolean('is_published')->default(false);      // true = tampil untuk pelanggan
            $t->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $t->unsignedSmallInteger('sort')->default(0);
            $t->string('kind', 10)->default('main');          // main | extra
            $t->text('mobil_text')->nullable();               // kolom MOBIL (baris dipisah newline)
            $t->text('rute_text')->nullable();                // kolom TGL & RUTE (baris dipisah newline)
            $t->unsignedSmallInteger('qty')->nullable();      // UNIT
            $t->unsignedSmallInteger('days')->nullable();     // HARI
            $t->unsignedBigInteger('price')->nullable();      // HARGA / HARI
            $t->unsignedBigInteger('amount')->default(0);     // JUMLAH
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
