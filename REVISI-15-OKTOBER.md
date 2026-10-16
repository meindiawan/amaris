# Revisi 15 Oktober 2026

Tidak ada migration baru (aman, tidak menumpuk). Jalankan seperti biasa; jika database sudah termigrasi, tidak ada yang perlu dijalankan.

## Admin
- Dashboard: tile **Unit Ready** & **Driver Ready**, daftar nopol/driver yang ready, dan tabel **Mobil Keluar / Masuk Terbaru**. Menu & statistik Carousel Promo dihapus dari admin.
- Daftar Booking (form booking order): supir yang sudah ditugaskan pada rentang tanggal yang bentrok **tidak muncul** di daftar Driver (otomatis muncul lagi jika tanggal diubah).
- Armada (Nopol): tombol "Cetak Semua Barcode" dan filter Di garasi / Sedang keluar / Nonaktif dihapus; kolom "Lokasi" jadi **Status kendaraan**.
- Tombol **Detail** di Armada: riwayat keluar-masuk (nopol, jenis kendaraan, tanggal keluar/masuk, dibawa oleh siapa), filter tanggal, dan **Export Excel (.xlsx)**.
- Mobil Keluar / Masuk: kolom nopol tidak lagi terpotong/pecah baris di zoom 100%.

## Website pengunjung
- Form booking: bagian "Data penyewa" dihapus (data penyewa diisi lewat chat WhatsApp). Teks paket: "Anda yang mengendarai sendiri".
- Carousel promo dihapus, diganti hero statis dari kode.
- Halaman baru: **/galeri** (22 foto, bisa diperbesar) dan **/artikel** (3 artikel). Konten artikel ada di `app/Support/Artikel.php` dan `resources/views/artikel/isi/`.
- Cek Booking: booking dari formulir baru cukup dengan kode booking.
