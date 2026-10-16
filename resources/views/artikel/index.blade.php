@extends('layouts.site')
@section('title','Informasi Rental Mobil Semarang — Artikel Amaris Ren Car')
@section('description','Artikel dan panduan memilih rental mobil harian, tarif sewa, dan tips perjalanan dari Amaris Ren Car.')
@section('content')
<section class="page-head dark-head"><div class="container"><h1 class="banner-title">INFORMASI RENTAL MOBIL SEMARANG</h1></div></section>
<section class="gallery-section"><div class="container"><div class="artikel-grid">
@foreach($artikels as $slug=>$a)
 <article class="artikel-card"><a class="artikel-img" href="{{ route('artikel.show',$slug) }}"><img src="{{ asset($a['image']) }}" alt="{{ $a['title'] }}" loading="lazy"></a>
  <div class="artikel-body"><h2><a href="{{ route('artikel.show',$slug) }}">{{ $a['title'] }}</a></h2><p>{{ $a['excerpt'] }}</p><a class="read-more" href="{{ route('artikel.show',$slug) }}">READ MORE »</a></div></article>
@endforeach
</div></div></section>
@endsection
