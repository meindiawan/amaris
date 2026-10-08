# Amaris Ren Car — Modul Operasional (unit, supir, keluar-masuk, pembayaran, setoran, invoice PDF)

Semua fitur lama (landing page, booking online, promo, armada, FAQ, pengaturan) tetap utuh. Modul ini hanya MENAMBAH tabel/kolom/menu.

## Cara menjalankan
```bash
php artisan migrate        # menambah tabel baru (aman, data lama tidak disentuh)
php artisan serve
```
Database SQLite di ZIP ini sudah dimigrasi. Untuk MySQL/Hostinger cukup jalankan `php artisan migrate` di server.
PDF memakai dompdf yang sudah dibundel di `lib/dompdf` (tanpa composer). Server butuh ekstensi PHP: gd, mbstring, dom.
Pastikan folder `storage/` dan `public/uploads/` bisa ditulis.

## Menu admin baru
- Unit Kendaraan: plat, jenis, STNK (no., atas nama, masa berlaku, scan), pajak, pemilik/mitra, barcode Code128 otomatis + cetak label.
- Daftar Supir: nama, HP, alamat, NIK, SIM (golongan, masa berlaku), foto.
- Keluar Masuk: scan barcode (alat USB / kamera HP) atau ketik plat, catat keluar/masuk, KM, BBM, kondisi. Status booking ikut berubah.
- Pembayaran: DP, cicilan, pelunasan, deposit (juga dari detail booking).
- Setoran: per unit / supir / booking, rekap per unit.
- Invoice: otomatis dari booking, bisa diedit (BBM, tol, parkir, overtime, diskon), PDF mengikuti INVOICE_AMARIS.xlsx.
- Detail booking: Penugasan Unit & Supir + Booking Order (salin / kirim WhatsApp), Pembayaran, Invoice.
- Dashboard: unit keluar, belum ditugaskan, belum lunas, peringatan STNK/pajak/SIM <= 30 hari.
- Pengaturan: bank, rekening, penanda tangan, awalan nomor invoice.

## Sisi pelanggan
- /booking/{kode}: Booking Order (setelah dikonfirmasi & unit ditugaskan) dan tombol Lihat/Unduh Invoice (setelah "dikirim").
- /invoice/{token}: tampilan invoice + unduh PDF + cetak.
- /cek-booking: buka booking dengan kode + 4 digit akhir no. HP.

Ganti logo/tanda tangan invoice: timpa public/images/invoice/logo.png dan ttd.png.
