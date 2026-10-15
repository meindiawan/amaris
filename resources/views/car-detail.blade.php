@php
 $rp = fn($n) => 'Rp '.number_format($n,0,',','.');
 $wa = $settings['whatsapp'] ?? '6285157024883';
 $driverFee = (int)($settings['driver_fee'] ?? 150000); $allinFee = (int)($settings['allin_fee'] ?? 350000); $deliveryFee = (int)($settings['delivery_fee'] ?? 0);
 $office = $settings['address'] ?? 'Kantor Amaris Ren Car';
 $service = old('service_type','self_drive'); $pm = old('pickup_method','office'); $rm = old('return_method','office');
@endphp
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $car->name }} — Amaris Ren Car</title><link rel="icon" href="{{ asset('images/logo-amaris.png') }}"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('css/amaris.css') }}"><link rel="stylesheet" href="{{ asset('css/booking.css') }}"></head><body><header class="navbar"><div class="container nav-inner"><a class="brand" href="/"><img src="{{ asset('images/logo-amaris.png') }}"></a><nav class="nav-links"><a href="/#armada">Armada</a><a class="nav-cta" href="#booking">Booking Sekarang ↗</a></nav></div></header><main class="car-detail-page"><div class="container"><a class="back-link" href="/#armada">← Kembali ke armada</a><div class="detail-grid"><div class="detail-image"><img src="{{ $car->image }}" alt="{{ $car->name }}"></div><div class="detail-copy"><span class="eyebrow dark">AMARIS FLEET</span><h1>{{ $car->name }}</h1><p class="detail-desc">{{ $car->description ?: 'Mobil siap digunakan untuk perjalanan keluarga, bisnis, maupun kebutuhan harian.' }}</p><div class="specs"><div><small>Tipe</small><b>{{ $car->type }}</b></div><div><small>Transmisi</small><b>{{ $car->transmission }}</b></div><div><small>Kapasitas</small><b>{{ $car->seats }} orang</b></div><div><small>Status</small><b>{{ $car->is_available?'Tersedia':'Tidak tersedia' }}</b></div></div><div class="detail-price"><small>Mulai dari</small><strong>Rp {{ number_format($car->price,0,',','.') }} <i>/ hari</i></strong></div><div class="detail-cta">@if($car->is_available)<a class="btn btn-primary" href="#booking">Booking Sekarang ↓</a>@endif<a class="btn btn-outline" href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo Amaris Ren Car, saya ingin bertanya tentang '.$car->name.'.') }}" target="_blank" rel="noopener">Tanya via WhatsApp ↗</a></div></div></div></div>

<section class="booking-section" id="booking"><div class="container">
 <div class="booking-head"><span class="eyebrow dark">BOOKING ONLINE</span><h2>Booking {{ $car->name }}</h2><p>Isi formulir di bawah. Setelah dikirim, Anda otomatis diarahkan ke WhatsApp admin untuk konfirmasi ketersediaan dan pembayaran.</p></div>

 @if($errors->any())<div class="form-alert" id="formAlert"><b>Mohon periksa kembali isian Anda:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

 @if(!$car->is_available)
  <div class="form-alert info">Mobil ini sedang tidak tersedia untuk disewa. Silakan pilih mobil lain di <a href="/#armada">katalog armada</a> atau tanya admin via WhatsApp.</div>
 @else
 <form id="bookingForm" class="booking-layout" method="POST" action="{{ route('booking.store',$car) }}" data-price="{{ $car->price }}" data-delivery="{{ $deliveryFee }}" data-booked='@json($booked->map(fn($b)=>[$b->start_at->toIso8601String(),$b->end_at->toIso8601String()]))'>
  @csrf
  <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

  <div class="booking-steps">
   <fieldset class="bcard"><legend><span>1</span> Pilih paket sewa</legend>
    <div class="opt-grid three">
     <label class="opt"><input type="radio" name="service_type" value="self_drive" data-fee="0" {{ $service==='self_drive'?'checked':'' }}><span class="opt-body"><b>Mobil Saja</b><small>Lepas kunci — Anda yang mengendarai sendiri.</small><i>Tanpa biaya tambahan</i></span></label>
     <label class="opt"><input type="radio" name="service_type" value="with_driver" data-fee="{{ $driverFee }}" {{ $service==='with_driver'?'checked':'' }}><span class="opt-body"><b>Mobil + Supir</b><small>Disetiri supir kami. BBM &amp; biaya perjalanan ditanggung penyewa.</small><i>+ {{ $rp($driverFee) }} / hari</i></span></label>
     <label class="opt"><input type="radio" name="service_type" value="all_in" data-fee="{{ $allinFee }}" {{ $service==='all_in'?'checked':'' }}><span class="opt-body"><b>All In</b><small>Terima beres: supir, BBM, dan kebutuhan perjalanan lainnya.</small><i>+ {{ $rp($allinFee) }} / hari</i></span></label>
    </div></fieldset>

   <fieldset class="bcard"><legend><span>2</span> Jadwal sewa</legend>
    <div class="fgrid">
     <label>Tanggal ambil (check-in)<input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}" required></label>
     <label>Jam ambil<input type="time" name="start_time" id="startTime" value="{{ old('start_time','08:00') }}" required></label>
     <label>Tanggal kembali (check-out)<input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}" required></label>
     <label>Jam kembali<input type="time" name="end_time" id="endTime" value="{{ old('end_time','08:00') }}" required></label>
    </div>
    <p class="hint" id="durationHint">Durasi dihitung per 24 jam, minimal 1 hari.</p>
    <p class="hint warn" id="conflictHint" hidden>⚠ Mobil sudah dibooking pada tanggal tersebut. Silakan pilih tanggal lain.</p>
    @if($booked->count())<div class="booked-list"><small>Jadwal yang sudah terisi:</small>@foreach($booked as $b)<span>{{ $b->start_at->format('d M') }} – {{ $b->end_at->format('d M Y') }}</span>@endforeach</div>@endif
   </fieldset>

   <fieldset class="bcard"><legend><span>3</span> Serah terima &amp; pengembalian</legend>
    <p class="sub">Mobil diambil / diantar</p>
    <div class="opt-grid two">
     <label class="opt"><input type="radio" name="pickup_method" value="office" {{ $pm==='office'?'checked':'' }}><span class="opt-body"><b>Ambil di kantor</b><small>{{ $office }}</small></span></label>
     <label class="opt"><input type="radio" name="pickup_method" value="delivery" {{ $pm==='delivery'?'checked':'' }}><span class="opt-body"><b>Antar ke lokasi saya</b><small>Mobil diantar ke alamat Anda{{ $deliveryFee ? ' (+ '.$rp($deliveryFee).')' : '' }}.</small></span></label>
    </div>
    <label class="full addr" id="pickupWrap" {{ $pm==='delivery'?'':'hidden' }}>Alamat pengantaran<textarea name="pickup_address" id="pickupAddr" rows="2" placeholder="Alamat lengkap + patokan, mis. Hotel / rumah / stasiun">{{ old('pickup_address') }}</textarea></label>
    <p class="sub">Mobil dikembalikan / dijemput</p>
    <div class="opt-grid two">
     <label class="opt"><input type="radio" name="return_method" value="office" {{ $rm==='office'?'checked':'' }}><span class="opt-body"><b>Kembalikan ke kantor</b><small>Anda mengantar mobil ke kantor kami.</small></span></label>
     <label class="opt"><input type="radio" name="return_method" value="pickup" {{ $rm==='pickup'?'checked':'' }}><span class="opt-body"><b>Jemput di lokasi saya</b><small>Mobil dijemput dari alamat Anda{{ $deliveryFee ? ' (+ '.$rp($deliveryFee).')' : '' }}.</small></span></label>
    </div>
    <label class="full addr" id="returnWrap" {{ $rm==='pickup'?'':'hidden' }}>Alamat penjemputan<textarea name="return_address" id="returnAddr" rows="2" placeholder="Alamat lengkap + patokan">{{ old('return_address') }}</textarea></label>
   </fieldset>

   <fieldset class="bcard"><legend><span>4</span> Data penyewa</legend>
    <div class="fgrid">
     <label>Nama lengkap (sesuai KTP)<input name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" autocomplete="name"></label>
     <label>No. WhatsApp<input name="phone" type="tel" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" autocomplete="tel"></label>
     <label>NIK (No. KTP, 16 digit)<input name="nik" value="{{ old('nik') }}" required inputmode="numeric" maxlength="16" pattern="[0-9]{16}" placeholder="16 digit angka"></label>
     <label>Email <em>(opsional)</em><input name="email" type="email" value="{{ old('email') }}" autocomplete="email"></label>
     <label class="full">Alamat lengkap penyewa<textarea name="address" rows="2" required placeholder="Sesuai domisili saat ini">{{ old('address') }}</textarea></label>
     <label id="simWrap">No. SIM A <em>(untuk paket lepas kunci)</em><input name="sim_number" value="{{ old('sim_number') }}" maxlength="40"></label>
     <label>Jumlah penumpang <em>(opsional)</em><input name="passengers" type="number" min="1" max="{{ max(1,$car->seats) }}" value="{{ old('passengers') }}"></label>
     <label class="full">Tujuan / keperluan <em>(opsional)</em><input name="destination" value="{{ old('destination') }}" maxlength="200" placeholder="Mis. wisata Yogyakarta, acara keluarga, dinas luar kota"></label>
     <label class="full">Catatan tambahan <em>(opsional)</em><textarea name="notes" rows="2" placeholder="Permintaan khusus: kursi bayi, rute, jam jemput, dll">{{ old('notes') }}</textarea></label>
    </div>
    <label class="agree"><input type="checkbox" name="agree" value="1" {{ old('agree')?'checked':'' }} required> <span>Saya menyatakan data di atas benar dan menyetujui syarat &amp; ketentuan sewa. <em>{{ $settings['terms'] ?? '' }}</em></span></label>
   </fieldset>
  </div>

  <aside class="booking-summary"><div class="sum-card">
   <img src="{{ $car->image }}" alt="{{ $car->name }}"><h3>{{ $car->name }}</h3><small class="sum-sub">{{ $car->type }} · {{ $car->transmission }} · {{ $car->seats }} seat</small>
   <div class="srow"><span>Harga sewa / hari</span><b>{{ $rp($car->price) }}</b></div>
   <div class="srow"><span>Durasi</span><b id="sumDays">—</b></div>
   <div class="srow"><span id="sumSvcLabel">Paket</span><b id="sumSvc">—</b></div>
   <div class="srow" id="sumDelivRow" hidden><span>Antar / jemput</span><b id="sumDeliv"></b></div>
   <div class="stotal"><span>Estimasi total</span><strong id="sumTotal">{{ $rp($car->price) }}</strong></div>
   <button class="btn btn-primary" type="submit" id="submitBtn">Booking via WhatsApp →</button>
   <small class="sum-note">Ini hanya estimasi. Biaya final (deposit, kelebihan jam, BBM, tol, dll) dikonfirmasi admin sebelum serah terima.</small>
  </div></aside>
 </form>
 @endif
</div></section>
</main>
<footer class="detail-foot"><div class="container"><span>© {{ date('Y') }} Amaris Ren Car</span>@if(str_contains((string)$car->image,'wikimedia'))<span>Foto: Wikimedia Commons (CC BY-SA)</span>@endif</div></footer>
<a class="wa-float" href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo Amaris Ren Car, saya ingin bertanya tentang rental '.$car->name.'.') }}" target="_blank" rel="noopener" aria-label="Hubungi WhatsApp"><span class="wa-icon">◔</span><span class="wa-text">Chat Admin</span></a>
<script src="{{ asset('js/booking.js') }}"></script></body></html>
