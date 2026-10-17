<?php
namespace Database\Seeders;
use App\Models\Car;
use App\Models\Promo;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  User::updateOrCreate(['email'=>'admin@amarisrencar.com'],['name'=>'Amaris Admin','password'=>Hash::make('admin12345')]);
  if(Car::count()===0){foreach([
   ['Toyota Innova Reborn','toyota-innova-reborn','MPV','Automatic',7,550000,'https://commons.wikimedia.org/wiki/Special:FilePath/Toyota_Innova_Reborn_2022.jpg?width=1200','Favorit'],['Toyota Fortuner','toyota-fortuner','SUV','Automatic',7,850000,'https://commons.wikimedia.org/wiki/Special:FilePath/2021_Toyota_Fortuner_2.4_VRZ_TRD_Sportivo_%28front%29%2C_Pakuwon_Mall%2C_West_Surabaya.jpg?width=1200','Premium'],['Honda Brio','honda-brio','City Car','Automatic',5,350000,'https://commons.wikimedia.org/wiki/Special:FilePath/2023_Honda_Brio_Satya_E_%28facelift%29.jpg?width=1200','Hemat'],['Toyota Avanza','toyota-avanza','MPV','Manual / Automatic',7,400000,'https://commons.wikimedia.org/wiki/Special:FilePath/2022_Toyota_Avanza_%28W100%29_1.5_G%2C_Pakuwon_Trade_Center%2C_West_Surabaya.jpg?width=1200','Best Seller']
  ] as $i=>$c) Car::create(array_combine(['name','slug','type','transmission','seats','price','image','badge'],$c)+['sort_order'=>$i]);}
  if(Promo::count()===0){foreach([
   ['AMARIS SPECIAL','Liburan nyaman,\ntanpa ribet.','Sewa mobil bersih, terawat, dan siap jalan untuk kebutuhan harian, keluarga, maupun perjalanan bisnis.','https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=2000&q=90'],['FAMILY TRIP','Perjalanan keluarga\njadi lebih tenang.','Pilihan MPV dan SUV dengan kabin nyaman untuk menemani perjalanan Anda.','https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=2000&q=90'],['BUSINESS & EVENT','Mobil siap untuk\nsetiap agenda.','Butuh kendaraan untuk meeting, acara, wedding, atau perjalanan dinas? Kami siap membantu.','https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=2000&q=90']
  ] as $i=>$p) Promo::create(['eyebrow'=>$p[0],'title'=>$p[1],'text'=>$p[2],'image'=>$p[3],'sort_order'=>$i]);}
  if(Testimonial::count()===0){foreach([['Rizky A.','Customer','Mobilnya bersih, prosesnya cepat, dan admin responsif. Sangat membantu untuk perjalanan keluarga kami.'],['Nadia P.','Customer','Pertama kali sewa di Amaris Ren Car dan puas. Kondisi mobil sesuai foto dan tidak ribet saat serah terima.'],['Fajar M.','Customer','Pelayanannya ramah dan pilihan mobilnya cukup lengkap. Next trip pasti sewa lagi di sini.']] as $i=>$t) Testimonial::create(['name'=>$t[0],'role'=>$t[1],'text'=>$t[2],'initial'=>substr($t[0],0,1),'sort_order'=>$i]);}
  if(Faq::count()===0){foreach([['Apakah mobil bisa disewa untuk luar kota?','Bisa. Silakan hubungi admin untuk memastikan unit dan ketentuan perjalanan.'],['Apakah harga yang tampil sudah termasuk sopir?','Harga katalog adalah harga sewa unit. Pada formulir booking Anda bisa memilih Mobil Saja, Mobil + Supir, atau All In (supir + BBM dll); estimasi biayanya tampil otomatis.'],['Bagaimana cara melakukan pemesanan?','Pilih mobil, klik "Booking", lalu isi formulir (paket sewa, tanggal, lokasi, dan data diri). Admin akan menghubungi Anda via WhatsApp untuk konfirmasi.'],['Apakah bisa request unit tertentu?','Bisa selama unit tersedia. Admin akan membantu mengecek pilihan yang paling sesuai.']] as $i=>$f) Faq::create(['question'=>$f[0],'answer'=>$f[1],'sort_order'=>$i]);}
  foreach(['whatsapp'=>'6285157024883','email'=>'info@amarisrencar.com','instagram'=>'#','tiktok'=>'#','address'=>'Semarang, Jawa Tengah','about'=>'Amaris Ren Car hadir untuk membuat setiap perjalanan terasa lebih nyaman, aman, dan praktis.','driver_fee'=>'150000','allin_fee'=>'350000','delivery_fee'=>'0','terms'=>'Dokumen identitas dan ketentuan penyewaan mengikuti kebijakan Amaris Ren Car. Detail biaya, durasi, deposit, dan penggunaan kendaraan dikonfirmasi oleh admin sebelum serah terima.'] as $k=>$v) \DB::table('site_settings')->updateOrInsert(['key'=>$k],['value'=>$v,'updated_at'=>now(),'created_at'=>now()]);
 }
}
