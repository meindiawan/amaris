@extends('admin.layout') @section('content')
<div class="page-head"><div><span>GARASI</span><h2>Mobil Keluar / Masuk</h2><p>Scan barcode unit atau ketik plat untuk mencatat keluar & masuk.</p></div></div>
<form class="scan-box" method="GET" action="{{ route('admin.movements.scan') }}">
<input name="code" id="scan" autofocus autocomplete="off" placeholder="Scan barcode / ketik plat, lalu Enter">
<button class="btn">Cari Unit</button><button type="button" class="btn secondary" id="camBtn" style="display:none">📷 Kamera</button>
<small>Pemindai barcode USB bekerja seperti keyboard — arahkan kursor ke kolom ini lalu scan. Di HP, tombol Kamera muncul bila browser mendukung.</small></form>
<video id="camVideo" playsinline style="display:none;width:100%;max-width:420px;border-radius:14px;margin-bottom:16px"></video>
<div class="unit-grid">@foreach($vehicles as $v)<a class="unit-card {{ $v->is_out?'is-out':'' }}" href="{{ route('admin.movements.create',['vehicle'=>$v->id]) }}"><span class="plate">{{ $v->plate_number }}</span><small>{{ $v->label }}</small>@if($v->is_out)<span class="status out">Keluar · {{ optional($v->last_moved_at)->format('d M H:i') }}</span>@else<span class="status in">Di garasi</span>@endif</a>@endforeach</div>
<div class="table-card"><form method="GET" class="search-box" style="margin-bottom:10px"><select name="vehicle" class="in" style="padding:10px;border-radius:11px"><option value="">Semua unit</option>@foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(request('vehicle')==$v->id)>{{ $v->plate_number }}</option>@endforeach</select><input type="date" name="date" value="{{ request('date') }}"><button class="btn">Filter</button></form>
<table><thead><tr><th>Waktu</th><th>Unit</th><th>Jenis</th><th>Supir / Booking</th><th>KM</th><th>BBM</th><th>Catatan</th><th></th></tr></thead><tbody>
@forelse($logs as $m)<tr><td>{{ $m->moved_at->format('d M Y H:i') }}<small>{{ $m->recorded_by }}</small></td><td><span class="plate">{{ $m->vehicle->plate_number ?? '-' }}</span></td><td><span class="status {{ $m->type==='keluar'?'out':'in' }}">{{ $m->type==='keluar'?'Keluar':'Masuk' }}</span></td><td>{{ $m->driver->name ?? '-' }}<small>{{ $m->booking->code ?? '' }} {{ $m->destination }}</small></td><td>{{ $m->odometer ?? '-' }}</td><td>{{ $m->fuel ?? '-' }}</td><td>{{ $m->condition_notes }}</td>
<td class="actions"><form method="POST" action="{{ route('admin.movements.destroy',$m) }}" onsubmit="return confirm('Hapus catatan ini?')">@csrf @method('DELETE')<button>Hapus</button></form></td></tr>
@empty<tr><td colspan="8" class="empty">Belum ada catatan.</td></tr>@endforelse</tbody></table></div>
@if($logs->hasPages())<div class="pager"><span>Halaman {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>@if($logs->previousPageUrl())<a class="btn secondary" href="{{ $logs->previousPageUrl() }}">← Sebelumnya</a>@endif @if($logs->nextPageUrl())<a class="btn secondary" href="{{ $logs->nextPageUrl() }}">Berikutnya →</a>@endif</div>@endif
<script>
(function(){var b=document.getElementById('camBtn');if(!('BarcodeDetector' in window)||!navigator.mediaDevices)return;b.style.display='';
b.onclick=async function(){var v=document.getElementById('camVideo');try{var s=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}});v.srcObject=s;v.style.display='block';await v.play();
var d=new BarcodeDetector({formats:['code_128','qr_code']});var t=setInterval(async function(){try{var r=await d.detect(v);if(r.length){clearInterval(t);s.getTracks().forEach(function(x){x.stop()});document.getElementById('scan').value=r[0].rawValue;document.getElementById('scan').form.submit();}}catch(e){}},400);}catch(e){alert('Kamera tidak bisa diakses.');}};})();
</script>
@endsection
