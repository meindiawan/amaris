<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Cek Booking — Amaris Ren Car</title><link rel="icon" href="{{ asset('images/logo-amaris.png') }}"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('css/amaris.css') }}"><link rel="stylesheet" href="{{ asset('css/booking.css') }}"></head><body>
<header class="navbar"><div class="container nav-inner"><a class="brand" href="/"><img src="{{ asset('images/logo-amaris.png') }}"></a><nav class="nav-links"><a href="/#armada">Armada</a></nav></div></header>
<main class="car-detail-page"><div class="container"><div class="success-card">
<span class="eyebrow dark">CEK BOOKING</span><h1>Lihat booking &amp; invoice Anda</h1><p>Masukkan kode booking (contoh <b>AMR-AB12CD</b>). Jika booking Anda dulu terdaftar dengan nomor HP, isi juga 4 digit terakhirnya.</p>
@if($errors->any())<p style="color:#b3372d;font-weight:700">{{ $errors->first() }}</p>@endif
<form method="POST" action="{{ route('booking.lookup.post') }}" style="display:grid;gap:12px;max-width:380px;margin:18px auto 0;text-align:left">@csrf
<input name="code" value="{{ old('code') }}" placeholder="Kode booking" required style="padding:14px;border:1px solid #ded4c8;border-radius:10px;font:inherit">
<input name="phone" value="{{ old('phone') }}" placeholder="4 digit terakhir no. HP (jika diminta)" inputmode="numeric" style="padding:14px;border:1px solid #ded4c8;border-radius:10px;font:inherit">
<button class="btn btn-primary">Lihat Booking</button></form>
</div></div></main></body></html>
