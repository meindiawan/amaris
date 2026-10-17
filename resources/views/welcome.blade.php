<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#201735">
    <meta name="description" content="Amaris Ren Car — katalog rental mobil nyaman, aman, dan terpercaya.">
    <title>Amaris Ren Car — Rental Mobil</title>
    <link rel="icon" href="{{ asset('images/logo-amaris.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/amaris.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}?v={{ @filemtime(public_path('css/pages.css')) }}">
</head>
<body>
<header class="navbar" id="navbar">
    <div class="container nav-inner">
        <a class="brand" href="#home"><img src="{{ asset('images/logo-amaris.png') }}" alt="Amaris Ren Car"></a>
        <button class="menu-toggle" id="menuToggle" aria-label="Buka menu"><span></span><span></span><span></span></button>
        <nav class="nav-links" id="navLinks">
            <a href="#home">Beranda</a><a href="#armada">Armada</a><a href="{{ url('/galeri') }}">Galeri</a><a href="{{ url('/artikel') }}">Artikel</a><a href="#video">Video</a><a href="#tentang">Tentang</a><a href="#keunggulan">Keunggulan</a><a href="#testimoni">Testimoni</a><a href="#faq">FAQ</a>
            <a class="nav-cta" href="#armada">Pilih Mobil <span>↗</span></a>
        </nav>
    </div>
</header>

<main id="home">
<section class="hero" id="promo">
    <div class="hero-slider" id="heroSlider">
        <article class="hero-slide active hero-fallback">
            <div class="hero-overlay"></div>
            <div class="container hero-content">
                <div class="hero-copy">
                    <span class="eyebrow">AMARIS REN CAR</span>
                    <h1>Temukan mobil yang<br><em>pas untuk perjalanan.</em></h1>
                    <p>Rental mobil nyaman, terawat, dan siap menemani perjalanan keluarga, bisnis, maupun acara spesial Anda — lengkap dengan pilihan supir profesional.</p>
                    <div class="hero-actions"><a href="#armada" class="btn btn-primary">Lihat Armada <span>→</span></a><a href="{{ url('/galeri') }}" class="btn btn-glass">Galeri Foto</a></div>
                    <ul class="hero-pills"><li>✓ Armada terawat</li><li>✓ Supir profesional</li><li>✓ Booking via WhatsApp</li></ul>
                </div>
            </div>
        </article>
    </div>
</section>
<section class="catalog-intro" id="armada">
    <div class="container">
        <div class="catalog-head">
            <div><span class="eyebrow dark">KATALOG ARMADA</span><h2>Pilih mobil sesuai<br><em>kebutuhan Anda.</em></h2><p>Semua unit tampil langsung di katalog. Pilih mobil, isi formulir booking (paket, tanggal, lokasi), lalu Anda langsung diarahkan ke WhatsApp admin untuk konfirmasi.</p></div>
            <div class="catalog-note"><span class="catalog-note-icon">✓</span><div><strong>Siap untuk disewa</strong><small>Admin siap membantu memilih unit</small></div></div>
        </div>
        <div class="filter-row" id="carFilters"><button class="filter active" data-filter="all">Semua</button><button class="filter" data-filter="MPV">MPV</button><button class="filter" data-filter="SUV">SUV</button><button class="filter" data-filter="City Car">City Car</button></div>
        <div class="fleet-grid catalog-grid" id="fleetGrid">
            @foreach($cars as $car)
            <article class="car-card" data-type="{{ $car->type }}">
                <a class="car-image" href="{{ url('/armada/'.$car->slug) }}">
                    <img src="{{ $car->image }}" alt="{{ $car->name }}" loading="lazy">
                    @if($car->badge)<span class="car-badge">{{ $car->badge }}</span>@endif
                    <span class="image-arrow">↗</span>
                </a>
                <div class="car-body">
                    <div class="car-meta"><span>{{ $car->type ?: 'AMARIS FLEET' }}</span><span class="available"><i></i> Tersedia</span></div>
                    <h3><a class="car-name-link" href="{{ url('/armada/'.$car->slug) }}">{{ $car->name }}</a></h3>
                    <div class="spec-pills"><span>👥 {{ $car->seats }} Seat</span><span>⚙ {{ $car->transmission }}</span></div>
                    <div class="car-bottom"><div><small>Mulai dari</small><strong>Rp {{ number_format($car->price,0,',','.') }} <i>/ hari</i></strong></div><a class="rent-link" href="{{ url('/armada/'.$car->slug) }}#booking">Booking <span>↗</span></a></div>
                </div>
            </article>
            @endforeach
        </div>
        @if($cars->count() === 0)<div class="empty-catalog">Belum ada armada yang ditambahkan. Silakan tambahkan melalui Admin → Armada.</div>@endif
    </div>
</section>

<section class="quick-benefits"><div class="container benefit-grid"><div><b>01</b><strong>Armada Terawat</strong><span>Siap dipakai dengan nyaman</span></div><div><b>02</b><strong>Harga Transparan</strong><span>Jelas sejak awal</span></div><div><b>03</b><strong>Admin Responsif</strong><span>Mudah dihubungi via WhatsApp</span></div><div><b>04</b><strong>Pilihan Fleksibel</strong><span>Untuk keluarga, bisnis & acara</span></div></div></section>

@php
 $docUrl = trim((string) ($settings['documentary_url'] ?? 'https://youtu.be/oOfvnOYGPVg'));
 $docId = ($docUrl !== '' && preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~', $docUrl, $dm)) ? $dm[1] : null;
@endphp
@if($docId)
<section class="section doc-video" id="video"><div class="container"><div class="doc-head"><span class="eyebrow">VIDEO DOKUMENTER</span><h2>Lihat langsung<br><em>perjalanan bersama Amaris.</em></h2><p>Tonton dokumenter singkat kami untuk mengenal layanan, armada, dan cara kami menemani perjalanan Anda.</p></div>
<div class="doc-frame" id="docFrame" data-id="{{ $docId }}"><button type="button" class="doc-play" id="docPlay" aria-label="Putar video dokumenter"><img src="https://i.ytimg.com/vi/{{ $docId }}/hqdefault.jpg" alt="Video dokumenter Amaris Ren Car" loading="lazy" onerror="this.style.display='none'"><span class="doc-btn"><svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span></button></div>
<p class="doc-link"><a href="https://www.youtube.com/watch?v={{ $docId }}" target="_blank" rel="noopener">Buka di YouTube ↗</a></p></div></section>
<script>(function(){var f=document.getElementById('docFrame'),b=document.getElementById('docPlay');if(!f||!b)return;b.addEventListener('click',function(){var i=document.createElement('iframe');i.src='https://www.youtube-nocookie.com/embed/'+f.dataset.id+'?autoplay=1&rel=0';i.title='Video dokumenter Amaris Ren Car';i.allow='accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share';i.setAttribute('allowfullscreen','');i.referrerPolicy='strict-origin-when-cross-origin';f.innerHTML='';f.appendChild(i);});})();</script>
@endif

<section class="section about" id="tentang"><div class="container"><div class="section-head"><div><span class="eyebrow dark">TENTANG AMARIS REN CAR</span><h2>Informasi &amp; panduan<br><em>rental mobil.</em></h2></div><a class="text-link" href="{{ url('/artikel') }}">Semua artikel <span>↗</span></a></div>
<div class="artikel-grid home-artikel">@foreach(\App\Support\Artikel::all() as $slug=>$a)<article class="artikel-card"><a class="artikel-img" href="{{ route('artikel.show',$slug) }}"><img src="{{ asset($a['image']) }}" alt="{{ $a['title'] }}" loading="lazy"></a><div class="artikel-body"><h3><a href="{{ route('artikel.show',$slug) }}">{{ $a['title'] }}</a></h3><p>{{ \Illuminate\Support\Str::limit($a['excerpt'],170,'...',true) }}</p><a class="read-more" href="{{ route('artikel.show',$slug) }}">READ MORE »</a></div></article>@endforeach</div></div></section>

<section class="section why" id="keunggulan"><div class="container why-grid"><div class="why-intro"><span class="eyebrow">KENAPA AMARIS?</span><h2>Rental yang dibuat<br>lebih <em>simple.</em></h2><p>Tidak perlu mengisi tujuan atau tanggal hanya untuk melihat pilihan mobil. Semua armada kami tampil seperti katalog agar Anda bisa membandingkan dengan cepat.</p><a class="btn btn-primary" href="#armada">Jelajahi Armada <span>↗</span></a></div><div class="why-list"><div class="why-item"><span>01</span><div><h3>Pilihan mobil jelas</h3><p>Foto, tipe, kapasitas, transmisi, dan harga terlihat langsung.</p></div></div><div class="why-item"><span>02</span><div><h3>Booking online</h3><p>Pilih paket mobil saja, dengan supir, atau all in, lalu isi tanggal dan lokasi.</p></div></div><div class="why-item"><span>03</span><div><h3>Informasi mudah diperbarui</h3><p>Admin dapat mengatur armada dan promo dari dashboard.</p></div></div><div class="why-item"><span>04</span><div><h3>Nyaman di semua perangkat</h3><p>Tampilan dirancang untuk laptop, tablet, dan HP.</p></div></div></div></div></section>

<section class="section how"><div class="container"><div class="section-head"><div><span class="eyebrow dark">CARA SEWA</span><h2>Sesederhana<br><em>itu.</em></h2></div></div><div class="steps"><div><span>01</span><h3>Pilih mobil</h3><p>Lihat katalog dan pilih unit yang sesuai.</p></div><div><span>02</span><h3>Isi formulir booking</h3><p>Pilih paket, tanggal, dan lokasi antar-jemput.</p></div><div><span>03</span><h3>Konfirmasi via WhatsApp</h3><p>Pesan booking terkirim ke WhatsApp admin dan dikonfirmasi di sana.</p></div></div></div></section>

<section class="section faq" id="faq"><div class="container faq-grid"><div><span class="eyebrow dark">PERTANYAAN UMUM</span><h2>Masih ada yang<br><em>ingin ditanyakan?</em></h2><p class="faq-intro">Jawaban singkat untuk pertanyaan yang paling sering ditanyakan sebelum menyewa mobil.</p></div><div class="faq-list">@forelse($faqs as $faq)<details><summary>{{ $faq->question }}<span>+</span></summary><p>{{ $faq->answer }}</p></details>@empty<details open><summary>Bagaimana cara menyewa mobil?<span>+</span></summary><p>Pilih unit dari katalog, klik Booking, lalu isi formulir. Admin akan menghubungi Anda untuk konfirmasi.</p></details>@endforelse</div></div></section>

<section class="section testimonials" id="testimoni"><div class="container"><div class="section-head"><div><span class="eyebrow dark">KATA MEREKA</span><h2>Pengalaman yang<br><em>berkesan.</em></h2></div><div class="testimonial-arrows"><button id="prevTesti">←</button><button id="nextTesti">→</button></div></div><div class="testi-window"><div class="testi-track" id="testiTrack">@foreach($testimonials as $testimonial)<article class="testi-card"><div class="quote">“</div><p>{{ $testimonial->text }}</p><div class="testi-user"><span>{{ $testimonial->initial }}</span><div><strong>{{ $testimonial->name }}</strong><small>{{ $testimonial->role }}</small></div><b>★★★★★</b></div></article>@endforeach</div></div></div></section>

<section class="cta-section"><div class="container cta-inner"><div><span class="eyebrow">SIAP BERANGKAT?</span><h2>Sudah menemukan<br><em>mobilnya?</em></h2><p>Pilih unit dari katalog lalu booking langsung dari website.</p></div><a class="btn btn-light" href="#armada">Pilih Mobil <span>↗</span></a></div></section>
</main>

<footer class="footer"><div class="container"><div class="footer-top"><div class="footer-brand"><img src="{{ asset('images/logo-amaris.png') }}" alt="Amaris Ren Car"><p>Rental mobil untuk perjalanan yang lebih nyaman, aman, dan praktis.</p></div><div><h4>Menu</h4><a href="#home">Beranda</a><a href="#armada">Armada</a><a href="{{ url('/galeri') }}">Galeri</a><a href="{{ url('/artikel') }}">Artikel</a><a href="#tentang">Tentang</a><a href="#testimoni">Testimoni</a><a href="#faq">FAQ</a></div><div><h4>Hubungi</h4><a class="fc-link" href="https://wa.me/{{ $settings['whatsapp'] ?? '6285157024883' }}" target="_blank" rel="noopener">@include('partials.brand-icon',['n'=>'wa']) WhatsApp Admin</a>@if(!empty($settings['facebook']))<a class="fc-link" href="{{ preg_match('#^https?://#i',$settings['facebook']) ? $settings['facebook'] : 'https://facebook.com/'.ltrim($settings['facebook'],'/@') }}" target="_blank" rel="noopener">@include('partials.brand-icon',['n'=>'fb']) Facebook</a>@endif<span>{{ $settings['email'] ?? 'info@amarisrencar.com' }}</span><span>{{ $settings['address'] ?? 'Semarang, Jawa Tengah' }}</span></div><div><h4>Ikuti Kami</h4><div class="socials">@php $soc=['fb'=>$settings['facebook'] ?? '','ig'=>$settings['instagram'] ?? '','tk'=>$settings['tiktok'] ?? '']; $lbl=['fb'=>'Facebook','ig'=>'Instagram','tk'=>'TikTok']; $base=['fb'=>'https://facebook.com/','ig'=>'https://instagram.com/','tk'=>'https://tiktok.com/@']; @endphp @foreach($soc as $k=>$u)@php $u=trim((string)$u); $href=($u===''||$u==='#') ? '#' : (preg_match('#^https?://#i',$u) ? $u : $base[$k].ltrim($u,'/@')); @endphp<a class="soc soc-{{ $k }}" href="{{ $href }}" @if($href!=='#')target="_blank" rel="noopener"@endif aria-label="{{ $lbl[$k] }}" title="{{ $lbl[$k] }}">@include('partials.brand-icon',['n'=>$k])</a> @endforeach</div></div></div><div class="footer-bottom"><span>© {{ date('Y') }} Amaris Ren Car. All rights reserved.</span><span>Rental Mobil • Comfort • Care • Journey</span><a class="footer-admin-btn" href="{{ route('admin.login') }}" title="Khusus pengelola website"><svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/></svg>Panel Admin</a></div></div></footer>

@include('partials.contact-float')
<script src="{{ asset('js/amaris.js') }}"></script>
</body>
</html>
