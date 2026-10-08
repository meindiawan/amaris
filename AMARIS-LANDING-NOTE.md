# Amaris Ren Car — Landing Page

Landing page sudah dibuat untuk Laravel 12 dan memakai logo asli di `public/images/logo-amaris.png`.

## File utama yang diganti/ditambahkan
- `resources/views/welcome.blade.php` — landing page lengkap.
- `routes/web.php` — data demo promo, armada, testimoni.
- `public/css/amaris.css` — seluruh styling responsive.
- `public/js/amaris.js` — carousel promo, mobile menu, testimonial slider, booking WhatsApp.
- `public/images/logo-amaris.png` — logo dari user.

## Setelah copy project
Jalankan:
```bash
php artisan serve
```
Lalu buka `http://127.0.0.1:8000`.

Jika memakai Vite/npm, halaman ini tidak bergantung pada build Vite untuk CSS/JS landing page.

## Penting
Nomor WhatsApp demo di landing page adalah `6281234567890`. Ganti dengan nomor admin sebenarnya di:
- `routes/web.php` pada link WA katalog.
- `resources/views/welcome.blade.php` pada footer dan tombol WA.

### Tahap berikutnya
Data promo carousel, katalog mobil, harga, status tersedia, dan testimoni sebaiknya dipindahkan ke database + halaman admin. Struktur landing page sudah dibuat supaya mudah dihubungkan ke data admin tersebut.
