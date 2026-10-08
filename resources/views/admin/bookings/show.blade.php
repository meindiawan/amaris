@extends('admin.layout') @section('content')
@php
 $rp = fn($n)=>'Rp '.number_format($n,0,',','.');
 $wa = 'https://wa.me/'.$booking->wa_phone.'?text='.urlencode('Halo '.$booking->customer_name.', kami dari Amaris Ren Car terkait booking '.$booking->code.' ('.$booking->car_name.', '.$booking->start_at->format('d M Y H:i').' – '.$booking->end_at->format('d M Y H:i').').');
@endphp
<div class="page-head"><div><span>BOOKING {{ $booking->code }}</span><h2>{{ $booking->customer_name }}</h2><p>Masuk {{ $booking->created_at->format('d M Y, H:i') }} · <span class="status st-{{ $booking->status }}">{{ $booking->status_label }}</span></p></div>
<div class="head-actions"><a class="btn wa" href="{{ $wa }}" target="_blank" rel="noopener">Chat WhatsApp ↗</a><a class="btn secondary" href="{{ route('admin.bookings.index') }}">← Kembali</a></div></div>
@if($conflict)<div class="alert error" style="margin:0 0 18px">⚠ Jadwal ini bentrok dengan booking <b>{{ $conflict->code }}</b> ({{ $conflict->customer_name }}, {{ $conflict->start_at->format('d M H:i') }} – {{ $conflict->end_at->format('d M H:i') }}) yang sudah dikonfirmasi.</div>@endif
<div class="detail-cols">
 <div class="detail-main">
  <div class="form-card"><h3>Mobil & Jadwal</h3><dl class="dl">
   <div><dt>Mobil</dt><dd>{{ $booking->car_name }}</dd></div><div><dt>Paket</dt><dd>{{ $booking->service_label }}</dd></div>
   <div><dt>Check-in (ambil)</dt><dd>{{ $booking->start_at->translatedFormat('l, d M Y') }} · {{ $booking->start_at->format('H:i') }}</dd></div>
   <div><dt>Check-out (kembali)</dt><dd>{{ $booking->end_at->translatedFormat('l, d M Y') }} · {{ $booking->end_at->format('H:i') }}</dd></div>
   <div><dt>Durasi</dt><dd>{{ $booking->days }} hari</dd></div><div><dt>Penumpang</dt><dd>{{ $booking->passengers ?: '-' }}</dd></div>
   <div class="wide"><dt>Serah terima mobil</dt><dd>{{ $booking->pickup_label }}</dd></div>
   <div class="wide"><dt>Pengembalian mobil</dt><dd>{{ $booking->return_label }}</dd></div>
   <div class="wide"><dt>Tujuan / keperluan</dt><dd>{{ $booking->destination ?: '-' }}</dd></div>
   @if($booking->notes)<div class="wide"><dt>Catatan pelanggan</dt><dd>{{ $booking->notes }}</dd></div>@endif
  </dl></div>
  <div class="form-card"><h3>Data Penyewa</h3><dl class="dl">
   <div><dt>Nama lengkap</dt><dd>{{ $booking->customer_name }}</dd></div><div><dt>WhatsApp</dt><dd>{{ $booking->phone }}</dd></div>
   <div><dt>NIK / No. KTP</dt><dd>{{ $booking->nik }}</dd></div><div><dt>Email</dt><dd>{{ $booking->email ?: '-' }}</dd></div>
   @if($booking->sim_number)<div><dt>No. SIM</dt><dd>{{ $booking->sim_number }}</dd></div>@endif
   <div class="wide"><dt>Alamat</dt><dd>{{ $booking->address }}</dd></div>
  </dl></div>
  @include('admin.bookings._ops')
 </div>
 <aside class="detail-side">
  <div class="form-card price-card"><h3>Rincian Biaya (estimasi)</h3>
   <div class="row"><span>Sewa mobil {{ $booking->days }} hari × {{ $rp($booking->price_per_day) }}</span><b>{{ $rp($booking->days*$booking->price_per_day) }}</b></div>
   @if($booking->service_fee_per_day)<div class="row"><span>{{ $booking->service_label }} {{ $booking->days }} hari × {{ $rp($booking->service_fee_per_day) }}</span><b>{{ $rp($booking->days*$booking->service_fee_per_day) }}</b></div>@endif
   @if($booking->delivery_fee)<div class="row"><span>Antar / jemput</span><b>{{ $rp($booking->delivery_fee) }}</b></div>@endif
   <div class="row total"><span>Total estimasi</span><b>{{ $rp($booking->total_estimate) }}</b></div>
   <small>Harga final disepakati dengan pelanggan (deposit, kelebihan jam, BBM, tol, dll).</small></div>
  @include('admin.bookings._ops_side')
  <form class="form-card" method="POST" action="{{ route('admin.bookings.status',$booking) }}">@csrf<h3>Status Booking</h3>
   <label class="stack">Status<select name="status">@foreach(\App\Models\Booking::STATUSES as $k=>$l)<option value="{{ $k }}" @selected($booking->status===$k)>{{ $l }}</option>@endforeach</select></label>
   <label class="stack">Catatan admin (internal)<textarea name="admin_notes" rows="4" placeholder="Deposit, supir yang ditugaskan, dll">{{ old('admin_notes',$booking->admin_notes) }}</textarea></label>
   <button class="btn" style="width:100%">Simpan Status →</button><small>Status <b>Dikonfirmasi / Berjalan</b> mengunci jadwal mobil di website.</small></form>
  <form method="POST" action="{{ route('admin.bookings.destroy',$booking) }}" onsubmit="return confirm('Hapus booking ini secara permanen?')">@csrf @method('DELETE')<button class="link-danger">Hapus booking ini</button></form>
 </aside>
</div>
@endsection
