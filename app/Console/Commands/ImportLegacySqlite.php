<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportLegacySqlite extends Command
{
    protected $signature = 'amaris:import-sqlite {--dry-run : Periksa jumlah data tanpa mengubah MySQL}';
    protected $description = 'Impor data katalog dan operasional dari database.sqlite lama ke MySQL tanpa menghapus data yang sudah ada.';

    public function handle(): int
    {
        $source = database_path('database.sqlite');
        if (!is_file($source)) {
            $this->error("Database SQLite lama tidak ditemukan: {$source}");
            return self::FAILURE;
        }

        config(['database.connections.legacy_sqlite.database' => $source]);
        DB::purge('legacy_sqlite');
        try {
            DB::connection('legacy_sqlite')->getPdo();
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            $this->error('Koneksi database gagal: '.$e->getMessage());
            return self::FAILURE;
        }

        // Urutan mengikuti relasi foreign key. Data MySQL dengan ID yang sama tidak ditimpa.
        $tables = [
            'users', 'cars', 'promos', 'testimonials', 'site_settings', 'faqs',
            'drivers', 'vehicles', 'bookings', 'invoices', 'invoice_items',
            'payments', 'vehicle_movements', 'setorans',
        ];
        $totalImported = 0;
        foreach ($tables as $table) {
            if (!Schema::connection('legacy_sqlite')->hasTable($table)) {
                $this->line("Lewati {$table}: tidak ada di SQLite.");
                continue;
            }
            if (!Schema::connection('mysql')->hasTable($table)) {
                $this->warn("Lewati {$table}: tabel belum ada di MySQL. Jalankan php artisan migrate terlebih dahulu.");
                continue;
            }
            $sourceColumns = Schema::connection('legacy_sqlite')->getColumnListing($table);
            $targetColumns = Schema::connection('mysql')->getColumnListing($table);
            $columns = array_values(array_intersect($sourceColumns, $targetColumns));
            if (!$columns) { $this->warn("Lewati {$table}: kolom yang cocok tidak ditemukan."); continue; }

            $imported = 0;
            foreach (DB::connection('legacy_sqlite')->table($table)->orderBy('id')->get() as $row) {
                $values = (array) $row;
                $values = array_intersect_key($values, array_flip($columns));
                if (isset($values['id']) && DB::table($table)->where('id', $values['id'])->exists()) {
                    continue;
                }
                if ($this->option('dry-run')) { $imported++; continue; }
                try {
                    DB::table($table)->insert($values);
                    $imported++;
                } catch (Throwable $e) {
                    $this->warn("{$table} ID ".($values['id'] ?? '?')." tidak diimpor: ".$e->getMessage());
                }
            }
            $this->info("{$table}: {$imported} baris ".($this->option('dry-run') ? 'siap diimpor' : 'diimpor').'.');
            $totalImported += $imported;
        }

        if ($this->option('dry-run')) {
            $this->comment("Dry run selesai. Perkiraan baris yang akan ditambahkan: {$totalImported}. Tidak ada data yang diubah.");
        } else {
            DB::purge('mysql');
            $this->info("Selesai. Total {$totalImported} baris ditambahkan. Data MySQL dengan ID yang sudah ada tidak ditimpa.");
            $this->comment('Jalankan php artisan optimize:clear lalu muat ulang website.');
        }
        return self::SUCCESS;
    }
}
