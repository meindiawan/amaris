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


---

## Revisi Oktober 2026

**Setelah mengganti file, jalankan:** `php artisan migrate` (menambah tabel `vehicle_logs`, kolom `drivers.sim_photo`, dan mengganti nomor WA ke 6285157024883). Data lama tidak dihapus; catatan keluar/masuk lama otomatis diimpor ke format baru.

1. **Nomor WA** admin: +62 851-5702-4883 (bisa diubah di Admin → Pengaturan).
2. **Booking via WhatsApp**: setelah formulir booking dikirim, data tersimpan di sistem lalu pelanggan langsung diarahkan ke WA admin dengan pesan yang sudah terisi.
3. **Armada (Nopol)**: form hanya nopol, jenis mobil, file STNK, file barcode BBM.
4. **Daftar Supir**: form hanya nama, no. WA, foto SIM.
5. **Mobil Keluar/Masuk**: satu baris = satu perjalanan. Nopol dari dropdown, atau pilih "Lainnya" lalu isi nopol, jenis kendaraan, rental. Kolom: nopol, jenis kendaraan, rental, tgl keluar, tgl masuk, user yang booking, invoice (belum dibuat / sudah dibuat / sudah terkirim), payment (belum / sudah + catatan penerima), driver (dropdown atau "Lainnya"), keterangan.
6–9. Menu **Payment, Setoran, Invoice, Booking Masuk** dihapus dari admin.
10. **Daftar Booking** (menu baru): admin memilih armada & supir yang tersedia, mengisi pelayanan / giat / standby / tujuan / kontak tamu, lalu **Salin teks** atau **Bagikan ke WA** (format Booking Order).
11. **Video dokumenter** tampil di halaman utama (bagian "Video"). Link YouTube bisa diganti di Admin → Pengaturan.
