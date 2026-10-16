@extends('layouts.site')
@section('title',$artikel['title'].' — Amaris Ren Car')
@section('description',\Illuminate\Support\Str::limit($artikel['excerpt'],155))
@section('content')
@php $waNum = \App\Support\Fmt::waNumber(\App\Support\Fmt::setting('whatsapp','6285157024883')); @endphp
<article class="artikel-page"><div class="container narrow">
 <a class="back-link" href="{{ route('artikel.index') }}">← Semua artikel</a>
 <h1>{{ $artikel['title'] }}</h1>
 <img class="artikel-cover" src="{{ asset($artikel['image']) }}" alt="{{ $artikel['title'] }}">
 <div class="artikel-isi">@include($artikel['view'], ['waNum'=>$waNum])</div>
 @if(count($lainnya))<aside class="artikel-related"><h3>📍 Artikel Terkait:</h3><ul>@foreach($lainnya as $slug=>$a)<li><a href="{{ route('artikel.show',$slug) }}">{{ $a['title'] }}</a></li>@endforeach</ul></aside>@endif
</div></article>
@endsection
