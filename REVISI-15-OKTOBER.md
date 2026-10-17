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

## Tambahan 17 Oktober
- Carousel/hero atas beranda kini menampilkan foto mobil (Alphard, HiAce Premio, Pajero Sport, Jeep Wrangler, Alphard Pengantin) yang berganti otomatis tiap 4 detik, bisa diklik lewat titik navigasi.
- Semua tombol di admin dirapikan dengan satu sistem gaya (admin.css, bagian "SISTEM TOMBOL ADMIN"): tombol utama, sekunder, Detail, Edit, Hapus, filter, pager, dan menu sidebar. Tanpa perubahan database/migrasi.
- Ikon asli (SVG): tombol melayang WhatsApp (hijau resmi, berdenyut); Facebook hanya di footer (tampil jika link diisi di Admin > Pengaturan > Facebook), footer (Hubungi & Ikuti Kami: WhatsApp, Facebook, Instagram, TikTok), dan semua ikon menu sidebar admin. Kolom "facebook" memakai tabel site_settings yang sudah ada — tanpa migrasi.
- Hero: kartu foto dihapus; foto mobil HD jadi latar header (tertutup warna ungu, samar) dan tampil sama di HP & komputer. Latar utama dimuat dari Unsplash (URL di public/css/pages.css, `.hero-slide.hero-fallback`); jika gagal dimuat otomatis memakai foto lokal public/images/galeri/amaris-15.jpg. Untuk memakai foto sendiri: taruh di public/images/ lalu ganti URL tersebut.
- FAQ: link "Tanya admin via WhatsApp" dihapus. Footer: "Ikuti Kami" = Facebook, Instagram, TikTok (isi link di Pengaturan). Alamat default: Semarang, Jawa Tengah (ubah alamat yang tersimpan di Admin > Pengaturan > Alamat Kantor).
