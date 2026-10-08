# Amaris Ren Car — Admin Panel

## Fitur
- Login admin
- Dashboard ringkas
- CRUD Carousel Promo + upload gambar
- CRUD Armada + harga/status + upload foto
- CRUD Testimoni
- Pengaturan WhatsApp, email, sosial media, alamat, deskripsi
- Ganti logo dari admin
- Landing page mengambil data dari database
- Halaman detail setiap mobil: `/armada/{slug}`

## Login awal
Email: `admin@amarisrencar.com`
Password: `admin12345`

Segera ganti password setelah deployment.

## Menjalankan
```bash
php artisan migrate --seed
php artisan serve
```
Jika project sebelumnya sudah memiliki database kosong dan migration lama, perintah di atas aman untuk database baru. Jangan memakai `migrate:fresh` pada database produksi karena akan menghapus seluruh tabel.

## Upload
Folder upload otomatis digunakan:
- `public/uploads/cars`
- `public/uploads/promos`

## Hostinger
Setelah database MySQL dibuat, ubah `.env`:
`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sesuai Hostinger. Jalankan migration/seed dari terminal SSH atau import database.

## Fitur tambahan versi katalog
- FAQ publik + CRUD FAQ dari admin
- Profil admin + ganti password
- Login dilindungi throttle percobaan
- Pengaturan syarat & ketentuan umum
- Logo admin/web menggunakan PNG transparan

## Booking online (baru)
Alur: pilih mobil → halaman detail → isi formulir booking → data masuk ke **Admin → Booking Masuk** → admin konfirmasi via WhatsApp.

**Formulir (halaman `/armada/{slug}#booking`)**
- Paket: *Mobil Saja (lepas kunci)*, *Mobil + Supir*, atau *All In (supir + BBM dll)*.
- Tanggal & jam ambil (check-in) dan kembali (check-out); durasi dihitung per 24 jam, minimal 1 hari.
- Serah terima: *ambil di kantor* atau *diantar ke alamat*; pengembalian: *kembalikan ke kantor* atau *dijemput di alamat*.
- Data penyewa: nama, WhatsApp, NIK (16 digit), alamat, email, No. SIM (paket lepas kunci), tujuan, jumlah penumpang, catatan.
- Estimasi biaya tampil otomatis. Jadwal yang sudah dikonfirmasi tidak bisa dibooking ganda.
- Perlindungan spam: honeypot + batas 8 booking/menit per pengunjung.

**Admin → Booking Masuk**
- Daftar booking + filter status + pencarian (nama/kode/HP/mobil), badge jumlah booking baru di menu.
- Detail booking lengkap, tombol *Chat WhatsApp* ke pelanggan, ubah status (Baru, Dikonfirmasi, Berjalan, Selesai, Dibatalkan), catatan internal.
- Status *Dikonfirmasi/Berjalan* mengunci jadwal mobil; sistem menolak konfirmasi jika bentrok dengan booking lain.
- Biaya supir / All In / antar-jemput diatur di **Admin → Pengaturan** (alamat kantor di sana juga dipakai sebagai lokasi ambil/kembali).

## Setelah update file
```bash
php artisan migrate
```
(aman untuk data yang sudah ada — hanya menambah tabel `bookings`, pengaturan biaya, dan mengganti foto contoh mobil.)

## Foto mobil
Foto contoh diganti dengan foto asli tiap model dari Wikimedia Commons (CC BY-SA, kredit tampil di halaman detail). Untuk foto unit milik sendiri, upload dari Admin → Armada Mobil → Edit.

## Catatan teknis
- Nama rute admin diperbaiki (`admin.cars.index`, dst.) — sebelumnya dashboard admin error 500 karena nama rute tidak cocok dengan menu.
- Zona waktu aplikasi: `Asia/Jakarta`, bahasa: `id`.
- Tombol *Login Admin* dipindah dari navbar ke footer (`Panel Admin`).
