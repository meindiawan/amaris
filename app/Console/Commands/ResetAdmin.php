<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ResetAdmin extends Command
{
    protected $signature = 'amaris:reset-admin
        {--email=admin@amarisrencar.com : Email admin}
        {--password= : Password baru (minimal 8 karakter)}
        {--name=Amaris Admin : Nama admin jika akun baru dibuat}';

    protected $description = 'Buat atau reset akun admin supaya bisa login ke /admin/login.';

    public function handle(): int
    {
        try {
            if (!Schema::hasTable('users')) {
                $this->error('Tabel users belum ada. Jalankan: php artisan migrate');
                return self::FAILURE;
            }
        } catch (Throwable $e) {
            $this->error('Koneksi database gagal: '.$e->getMessage());
            return self::FAILURE;
        }

        $email = trim((string) $this->option('email'));
        $password = (string) ($this->option('password') ?: $this->secret('Password baru'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');
            return self::FAILURE;
        }
        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            return self::FAILURE;
        }

        $this->line('User yang ada sekarang:');
        $this->table(['id', 'name', 'email'], User::query()->get(['id', 'name', 'email'])->toArray());

        // Cast 'hashed' di model User otomatis meng-hash, jadi isi plain text di sini.
        $user = User::where('email', $email)->first() ?? new User(['email' => $email, 'name' => $this->option('name')]);
        $user->password = $password;
        $user->save();

        $this->info("Admin siap: {$email}");
        $this->comment('Jalankan php artisan optimize:clear lalu login di /admin/login.');
        return self::SUCCESS;
    }
}
