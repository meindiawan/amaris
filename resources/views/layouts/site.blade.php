@php
 $waNum = \App\Support\Fmt::waNumber(\App\Support\Fmt::setting('whatsapp', '6285157024883'));
@endphp
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'Amaris Ren Car — Rental Mobil')</title>
<meta name="description" content="@yield('description', 'Amaris Ren Car — rental mobil nyaman, aman, dan terpercaya.')">
<link rel="icon" href="{{ asset('images/logo-amaris.png') }}"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/amaris.css') }}"><link rel="stylesheet" href="{{ asset('css/pages.css') }}?v={{ @filemtime(public_path('css/pages.css')) }}">
</head><body>
<header class="navbar scrolled" id="navbar"><div class="container nav-inner"><a class="brand" href="{{ url('/') }}"><img src="{{ asset('images/logo-amaris.png') }}" alt="Amaris Ren Car"></a>
<button class="menu-toggle" id="menuToggle" aria-label="Buka menu"><span></span><span></span><span></span></button>
<nav class="nav-links" id="navLinks"><a href="{{ url('/') }}">Beranda</a><a href="{{ url('/#armada') }}">Armada</a><a href="{{ url('/galeri') }}" class="{{ request()->is('galeri') ? 'is-on' : '' }}">Galeri</a><a href="{{ url('/artikel') }}" class="{{ request()->is('artikel*') ? 'is-on' : '' }}">Artikel</a><a href="{{ url('/#faq') }}">FAQ</a><a class="nav-cta" href="{{ url('/#armada') }}">Pilih Mobil <span>↗</span></a></nav></div></header>
<main class="page-main">@yield('content')</main>
<footer class="footer"><div class="container"><div class="footer-bottom"><span>© {{ date('Y') }} Amaris Ren Car. All rights reserved.</span><span>Rental Mobil • Comfort • Care • Journey</span></div></div></footer>
<a class="wa-float" href="https://wa.me/{{ $waNum }}?text={{ urlencode('Halo Amaris Ren Car, saya ingin bertanya tentang rental mobil.') }}" target="_blank" rel="noopener" aria-label="Hubungi WhatsApp"><span class="wa-icon">◔</span><span class="wa-text">Chat Admin</span></a>
<script src="{{ asset('js/amaris.js') }}"></script>
@stack('scripts')
</body></html>
