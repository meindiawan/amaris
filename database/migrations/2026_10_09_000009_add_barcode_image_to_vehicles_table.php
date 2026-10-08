<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'barcode_image')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('barcode_image')->nullable()->after('barcode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'barcode_image')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('barcode_image');
            });
        }
    }
};
