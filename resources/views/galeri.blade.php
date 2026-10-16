@extends('layouts.site')
@section('title','Galeri Foto Rental Mobil — Amaris Ren Car')
@section('description','Galeri foto armada Amaris Ren Car: Alphard, HiAce, Pajero Sport, Jeep Wrangler dan unit lainnya.')
@php
 $fotos = [
  1=>'Innova dan Alphard di garasi Amaris Rent Car', 2=>'Armada Amaris di area bandara', 3=>'Toyota Alphard hitam siap jalan', 4=>'Deretan Alphard hitam Amaris Rent Car', 5=>'Fortuner dan Alphard hitam',
  6=>'Pajero Sport abu-abu dan Alphard hitam', 7=>'Pajero Sport di area bandara', 8=>'Alphard hitam dengan dekorasi bunga untuk acara pernikahan', 9=>'Toyota HiAce Premio putih', 10=>'Dua unit HiAce Premio perak dan putih',
  11=>'HiAce Premio perak dan putih di area parkir', 12=>'HiAce Commuter perak dan putih', 13=>'Interior Alphard dengan kursi captain seat', 14=>'Kabin belakang Alphard yang lega', 15=>'Mitsubishi Pajero Sport hitam',
  16=>'Alphard hitam di garasi Amaris', 17=>'Toyota Avanza putih', 18=>'Jeep Wrangler Rubicon putih tampak depan', 19=>'Jeep Wrangler Rubicon tampak belakang', 20=>'Jeep Wrangler Rubicon tampak samping',
  21=>'Jeep Wrangler Rubicon putih', 22=>'Interior Jeep Wrangler Rubicon',
 ];
@endphp
@section('content')
<section class="page-head"><div class="container"><span class="eyebrow dark">GALERI</span><h1>Galeri Foto <em>Rental Mobil</em></h1><p>Sebagian armada Amaris Ren Car. Klik foto untuk memperbesar.</p></div></section>
<section class="gallery-section"><div class="container"><div class="gallery-grid" id="galGrid">
@foreach($fotos as $n=>$alt)
 <button type="button" class="gal-item" data-i="{{ $n-1 }}" aria-label="Perbesar foto: {{ $alt }}"><img src="{{ asset(sprintf('images/galeri/amaris-%02d.jpg',$n)) }}" alt="{{ $alt }}" loading="lazy" width="678" height="452"></button>
@endforeach
</div></div></section>
<div class="lightbox" id="lightbox" hidden role="dialog" aria-modal="true" aria-label="Pratinjau foto"><button type="button" class="lb-close" id="lbClose" aria-label="Tutup">×</button><button type="button" class="lb-nav lb-prev" id="lbPrev" aria-label="Foto sebelumnya">‹</button><figure><img id="lbImg" alt=""><figcaption id="lbCap"></figcaption></figure><button type="button" class="lb-nav lb-next" id="lbNext" aria-label="Foto berikutnya">›</button></div>
@endsection
@push('scripts')
<script>
(function(){var items=[].slice.call(document.querySelectorAll('.gal-item')),lb=document.getElementById('lightbox'),img=document.getElementById('lbImg'),cap=document.getElementById('lbCap'),cur=0;
function show(i){cur=(i+items.length)%items.length;var im=items[cur].querySelector('img');img.src=im.src;img.alt=im.alt;cap.textContent=im.alt+'  ('+(cur+1)+'/'+items.length+')';}
function open(i){show(i);lb.hidden=false;document.body.style.overflow='hidden';}
function close(){lb.hidden=true;document.body.style.overflow='';}
items.forEach(function(b){b.addEventListener('click',function(){open(+b.dataset.i);});});
document.getElementById('lbClose').onclick=close;document.getElementById('lbPrev').onclick=function(){show(cur-1);};document.getElementById('lbNext').onclick=function(){show(cur+1);};
lb.addEventListener('click',function(e){if(e.target===lb)close();});
document.addEventListener('keydown',function(e){if(lb.hidden)return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')show(cur-1);if(e.key==='ArrowRight')show(cur+1);});})();
</script>
@endpush
