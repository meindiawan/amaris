@extends('admin.layout') @section('content')
<div class="page-head"><div><span>BOOKING ORDER</span><h2>Daftar Booking</h2><p>Pilih armada &amp; supir yang tersedia, lengkapi detailnya, lalu bagikan laporan Booking Order ke klien lewat WhatsApp.</p></div>
<div class="head-actions"><form class="search-box" method="GET" action="{{ route('admin.orders.index') }}">@if($status)<input type="hidden" name="status" value="{{ $status }}">@endif<input name="q" value="{{ $q }}" placeholder="Cari nama, kode, giat, nopol…"><button class="btn">Cari</button></form><a class="btn" href="{{ route('admin.orders.create') }}">+ Booking Order Baru</a></div></div>
<div class="filter-tabs">
 <a href="{{ route('admin.orders.index',['q'=>$q?:null]) }}" class="{{ !$status?'on':'' }}">Semua <b>{{ $counts->sum() }}</b></a>
 @foreach(\App\Models\Booking::STATUSES as $k=>$label)<a href="{{ route('admin.orders.index',['status'=>$k,'q'=>$q?:null]) }}" class="{{ $status===$k?'on':'' }}">{{ $label }} <b>{{ $counts[$k] ?? 0 }}</b></a>@endforeach
</div>
<div class="order-grid">
@forelse($orders as $o)
 @php
  $text = $o->orderText();
  $wa = $o->wa_phone && strlen($o->wa_phone) >= 9 ? 'https://wa.me/'.$o->wa_phone.'?text='.urlencode($text) : 'https://wa.me/?text='.urlencode($text);
 @endphp
 <article class="order-card">
  <header><div><b>{{ $o->order_name ?: $o->customer_name }}</b><small>{{ $o->code }} · masuk {{ $o->created_at->format('d M Y H:i') }}</small></div><span class="status st-{{ $o->status }}">{{ $o->status_label }}</span></header>
  @unless($o->is_assigned)<div class="need-assign">⚠ {{ $o->vehicle_id ? 'Supir belum dipilih' : 'Armada dan supir belum dipilih' }} — klik <b>Edit</b> untuk melengkapi.</div>@endunless
  <div class="order-text" id="ot{{ $o->id }}">{{ $text }}</div>
  <div class="order-actions"><button type="button" class="btn secondary" onclick="navigator.clipboard.writeText(document.getElementById('ot{{ $o->id }}').innerText).then(()=>{this.textContent='Tersalin ✓'})">Salin teks</button><a class="btn wa" target="_blank" rel="noopener" href="{{ $wa }}">Bagikan ke WA ↗</a><a class="btn" href="{{ route('admin.orders.edit',$o) }}">Edit / Lengkapi</a>
  <form method="POST" action="{{ route('admin.orders.destroy',$o) }}" onsubmit="return confirm('Hapus booking ini secara permanen?')">@csrf @method('DELETE')<button class="link-danger" style="width:auto">Hapus</button></form></div>
 </article>
@empty
 <div class="table-card"><p class="empty" style="padding:30px">Belum ada booking{{ ($status||$q)?' yang cocok dengan filter':'' }}. Booking dari website akan muncul di sini, atau klik "+ Booking Order Baru".</p></div>
@endforelse
</div>
@if($orders->hasPages())<div class="pager"><span>Halaman {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>@if($orders->previousPageUrl())<a class="btn secondary" href="{{ $orders->previousPageUrl() }}">← Sebelumnya</a>@endif @if($orders->nextPageUrl())<a class="btn secondary" href="{{ $orders->nextPageUrl() }}">Berikutnya →</a>@endif</div>@endif
@endsection
