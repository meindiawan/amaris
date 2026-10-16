@extends('admin.layout') @section('content')
<div class="page-head"><div><span>GARASI</span><h2>Mobil Keluar / Masuk</h2><p>Satu baris = satu perjalanan. Status invoice &amp; payment bisa diubah langsung dari tabel.</p></div><a class="btn" href="{{ route('admin.movements.create') }}">+ Catat Mobil Keluar/Masuk</a></div>
<form class="scan-box" method="GET" action="{{ route('admin.movements.scan') }}">
<input name="code" id="scan" autocomplete="off" placeholder="Scan barcode / ketik nopol, lalu Enter">
<button class="btn">Cari Armada</button><button type="button" class="btn secondary" id="camBtn" style="display:none">📷 Kamera</button>
<small>Pemindai barcode USB bekerja seperti keyboard — arahkan kursor ke kolom ini lalu scan. Hasilnya membuka form catatan dengan nopol terisi.</small></form>
<video id="camVideo" playsinline style="display:none;width:100%;max-width:420px;border-radius:14px;margin-bottom:16px"></video>
<div class="unit-grid">@foreach($vehicles as $v)<a class="unit-card {{ $v->is_out?'is-out':'' }}" href="{{ route('admin.movements.create',['vehicle'=>$v->id]) }}"><span class="plate">{{ $v->plate_nb }}</span><small>{{ $v->label }}</small>@if($v->is_out)<span class="status out">Keluar · {{ optional($v->last_moved_at)->format('d M') }}</span>@else<span class="status in">Di garasi</span>@endif</a>@endforeach</div>
<div class="filter-tabs"><a href="{{ route('admin.movements.index') }}" class="{{ !$f?'on':'' }}">Semua <b>{{ $counts['all'] }}</b></a><a href="{{ route('admin.movements.index',['f'=>'out']) }}" class="{{ $f==='out'?'on':'' }}">Belum masuk <b>{{ $counts['out'] }}</b></a><a href="{{ route('admin.movements.index',['f'=>'unpaid']) }}" class="{{ $f==='unpaid'?'on':'' }}">Belum payment <b>{{ $counts['unpaid'] }}</b></a><a href="{{ route('admin.movements.index',['f'=>'noinvoice']) }}" class="{{ $f==='noinvoice'?'on':'' }}">Invoice belum terkirim <b>{{ $counts['noinvoice'] }}</b></a></div>
<div class="table-card scroll-x"><form method="GET" class="search-box" style="margin-bottom:10px">@if($f)<input type="hidden" name="f" value="{{ $f }}">@endif<input name="q" value="{{ $q }}" placeholder="Cari nopol, jenis, rental, booking, driver…"><select name="vehicle" class="in" style="padding:10px;border-radius:11px"><option value="">Semua nopol</option>@foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(request('vehicle')==$v->id)>{{ $v->plate_number }}</option>@endforeach</select><input type="date" name="date" value="{{ request('date') }}"><button class="btn">Filter</button></form>
<table class="log-table"><thead><tr><th>Nopol</th><th>Jenis kendaraan</th><th>Rental</th><th>Tgl keluar</th><th>Tgl masuk</th><th>User yg booking</th><th>Invoice</th><th>Payment</th><th>Driver</th><th>Keterangan</th><th></th></tr></thead><tbody>
@forelse($logs as $m)<tr>
<td class="plate-cell"><span class="plate">{{ $m->plate_nb }}</span></td>
<td>{{ $m->vehicle_type ?: '-' }}</td>
<td>{{ $m->rental_name ?: '-' }}</td>
<td>{{ $m->out_date->format('d M Y') }}</td>
<td>@if($m->in_date){{ $m->in_date->format('d M Y') }}@else<form method="POST" action="{{ route('admin.movements.quick',$m) }}" class="qf">@csrf @method('PATCH')<input type="date" name="in_date" min="{{ $m->out_date->toDateString() }}" value="{{ now()->toDateString() }}" title="Tanggal masuk"><button class="mini-btn" title="Tandai sudah masuk">Masuk ✓</button></form>@endif</td>
<td>{{ $m->booker_name ?: '-' }}</td>
<td><form method="POST" action="{{ route('admin.movements.quick',$m) }}" class="qf">@csrf @method('PATCH')<select name="invoice_status" class="sel inv-{{ $m->invoice_status }}" onchange="this.form.submit()">@foreach(\App\Models\VehicleLog::INVOICE as $k=>$l)<option value="{{ $k }}" @selected($m->invoice_status===$k)>{{ $l }}</option>@endforeach</select></form></td>
<td><form method="POST" action="{{ route('admin.movements.quick',$m) }}" class="qf pay-form">@csrf @method('PATCH')<input type="hidden" name="payment_note" value="{{ $m->payment_note }}"><select name="payment_status" class="sel pay-{{ $m->payment_status }}" data-prev="{{ $m->payment_status }}" data-note="{{ $m->payment_note }}">@foreach(\App\Models\VehicleLog::PAYMENT as $k=>$l)<option value="{{ $k }}" @selected($m->payment_status===$k)>{{ $l }}</option>@endforeach</select>@if($m->payment_status==='sudah' && $m->payment_note)<small>Diterima: {{ $m->payment_note }}</small>@endif</form></td>
<td>{{ $m->driver_name ?: '-' }}</td>
<td class="note-cell">{{ $m->notes }}</td>
<td class="actions"><a href="{{ route('admin.movements.edit',$m) }}">Edit</a><form method="POST" action="{{ route('admin.movements.destroy',$m) }}" onsubmit="return confirm('Hapus catatan ini?')">@csrf @method('DELETE')<button>Hapus</button></form></td></tr>
@empty<tr><td colspan="11" class="empty">Belum ada catatan. Klik "+ Catat Mobil Keluar/Masuk".</td></tr>@endforelse</tbody></table></div>
@if($logs->hasPages())<div class="pager"><span>Halaman {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>@if($logs->previousPageUrl())<a class="btn secondary" href="{{ $logs->previousPageUrl() }}">← Sebelumnya</a>@endif @if($logs->nextPageUrl())<a class="btn secondary" href="{{ $logs->nextPageUrl() }}">Berikutnya →</a>@endif</div>@endif
<script>
document.querySelectorAll('.pay-form select').forEach(function(s){s.addEventListener('change',function(){
 var f=s.form,n=f.querySelector('[name=payment_note]');
 if(s.value==='sudah'){var who=prompt('Payment diterima oleh siapa?',s.dataset.note||'');if(!who||!who.trim()){s.value=s.dataset.prev;return;}n.value=who.trim();}
 else{n.value='';}
 f.submit();});});
(function(){var b=document.getElementById('camBtn');if(!('BarcodeDetector' in window)||!navigator.mediaDevices)return;b.style.display='';
b.onclick=async function(){var v=document.getElementById('camVideo');try{var s=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}});v.srcObject=s;v.style.display='block';await v.play();
var d=new BarcodeDetector({formats:['code_128','qr_code']});var t=setInterval(async function(){try{var r=await d.detect(v);if(r.length){clearInterval(t);s.getTracks().forEach(function(x){x.stop()});document.getElementById('scan').value=r[0].rawValue;document.getElementById('scan').form.submit();}}catch(e){}},400);}catch(e){alert('Kamera tidak bisa diakses.');}};})();
</script>
@endsection
