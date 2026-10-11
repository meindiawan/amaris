@php
 $rp = fn($n) => 'Rp '.number_format($n,0,',','.');
 $wa = $settings['whatsapp'] ?? '6285157024883';
 $msg = "Halo Amaris Ren Car, saya sudah booking online.\nKode: {$booking->code}\nMobil: {$booking->car_name}\nPaket: {$booking->service_label}\nAmbil: ".$booking->start_at->format('d M Y H:i')."\nKembali: ".$booking->end_at->format('d M Y H:i')." ({$booking->days} hari)\nNama: {$booking->customer_name}\nMohon konfirmasinya. Terima kasih.";
@endphp
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Booking Diterima — Amaris Ren Car</title><link rel="icon" href="{{ asset('images/logo-amaris.png') }}"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('css/amaris.css') }}"><link rel="stylesheet" href="{{ asset('css/booking.css') }}"></head><body>
<header class="navbar"><div class="container nav-inner"><a class="brand" href="/"><img src="{{ asset('images/logo-amaris.png') }}"></a><nav class="nav-links"><a href="/#armada">Armada</a><a href="{{ route('booking.lookup') }}">Cek Booking</a></nav></div></header>
<main class="car-detail-page"><div class="container"><div class="success-card">
 <div class="success-icon">✓</div><span class="eyebrow dark">BOOKING DITERIMA</span><h1>Terima kasih, {{ $booking->customer_name }}!</h1>
 <p>Booking Anda sudah masuk ke sistem kami. Admin akan segera menghubungi Anda via WhatsApp di <b>{{ $booking->phone }}</b> untuk konfirmasi.</p>
 <div class="code-box"><small>Kode booking</small><strong>{{ $booking->code }}</strong></div>
 <dl class="sum-list">
  <div><dt>Mobil</dt><dd>{{ $booking->car_name }}</dd></div><div><dt>Paket</dt><dd>{{ $booking->service_label }}</dd></div>
  <div><dt>Ambil</dt><dd>{{ $booking->start_at->translatedFormat('d M Y') }}, {{ $booking->start_at->format('H:i') }}</dd></div>
  <div><dt>Kembali</dt><dd>{{ $booking->end_at->translatedFormat('d M Y') }}, {{ $booking->end_at->format('H:i') }}</dd></div>
  <div><dt>Durasi</dt><dd>{{ $booking->days }} hari</dd></div><div><dt>Serah terima</dt><dd>{{ $booking->pickup_label }}</dd></div>
  <div><dt>Pengembalian</dt><dd>{{ $booking->return_label }}</dd></div><div><dt>Estimasi total</dt><dd><b>{{ $rp($booking->total_estimate) }}</b></dd></div>
 </dl>
 <p class="hint">Estimasi belum final — biaya akhir dikonfirmasi admin.</p>
 @include('partials.booking-extra')
 <div class="detail-cta" style="justify-content:center"><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/{{ $wa }}?text={{ urlencode($msg) }}">Percepat via WhatsApp ↗</a><a class="btn btn-outline" href="/">Kembali ke Beranda</a></div>
</div></div></main></body></html>
