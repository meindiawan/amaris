# Revisi 21 Oktober 2026

1. **Form booking** — ada bagian "Data pemesan": nama pemboking, nama tamu (bisa dicentang "sama dengan pemboking"), nomor WhatsApp. Semuanya tersimpan & ikut ke pesan WA. (`car-detail.blade.php`, `booking.js`, `BookingController.php`)
2. **Bug ikon WhatsApp raksasa** di halaman detail mobil — gaya `.contact-float`/`.bi` belum dimuat di halaman itu; sekarang ada di `public/css/booking.css`.
3. **Pesan WA** berakhir di "Estimasi total" (kalimat "Mohon konfirmasinya…" dihapus).
4. **Testimoni** di landing page lebih besar (`public/css/amaris.css`).
5. **Daftar Booking (admin)** — teks Booking Order memuat order by, WA pemesan, dan nama tamu; WA pemesan bisa diedit di form (`Booking::orderText`, `orders/form.blade.php`).
6. **Keluar/Masuk (admin)** — kotak unit di atas hanya menampilkan mobil yang ada di garasi.
7. **Tanggal masuk** tidak bisa kurang dari tanggal keluar: batas `min` + pengecekan di form, validasi server, dan pengaman di model `VehicleLog`. Tanggal selesai di edit Booking Order juga tidak bisa sebelum tanggal mulai.

Tidak ada migrasi baru (memakai kolom `order_name`, `guest_name`, `guest_phone`, `phone` yang sudah ada).
