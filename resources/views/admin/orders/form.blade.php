@extends('admin.layout') @section('content')
@php
 $rp = fn($n)=>'Rp '.number_format($n,0,',','.');
 $isWeb = $order->exists && $order->is_web;
 $sd = old('start_date', $order->start_at->format('Y-m-d')); $st = old('start_time', $order->start_at->format('H:i'));
 $ed = old('end_date', $order->end_at->format('Y-m-d'));   $et = old('end_time', $order->end_at->format('H:i'));
@endphp
<div class="page-head"><div><span>{{ $order->exists ? 'BOOKING '.$order->code : 'BOOKING ORDER BARU' }}</span><h2>{{ $order->exists ? ($order->order_name ?: $order->customer_name) : 'Buat Booking Order' }}</h2><p>@if($order->exists)Masuk {{ $order->created_at->format('d M Y, H:i') }} · <span class="status st-{{ $order->status }}">{{ $order->status_label }}</span>@else Untuk pesanan yang masuk lewat chat WhatsApp.@endif</p></div>
<div class="head-actions">@if($order->exists && $order->wa_phone && strlen($order->wa_phone)>=9)<a class="btn wa" href="https://wa.me/{{ $order->wa_phone }}" target="_blank" rel="noopener">Chat WhatsApp ↗</a>@endif<a class="btn secondary" href="{{ route('admin.orders.index') }}">← Kembali</a></div></div>

@if($isWeb)
<div class="form-card"><h3>Data dari formulir website</h3><dl class="dl">
 <div><dt>Mobil dipesan</dt><dd>{{ $order->car_name }} · {{ $order->service_label }}</dd></div>@if($order->customer_name !== \App\Models\Booking::WEB_NAME)<div><dt>Pemboking</dt><dd>{{ $order->customer_name }} · {{ $order->phone }}</dd></div>@endif @if($order->guest_name)<div><dt>Nama tamu</dt><dd>{{ $order->guest_name }}</dd></div>@endif
 @if($order->nik && $order->nik !== '-')<div><dt>NIK</dt><dd>{{ $order->nik }}</dd></div>@endif<div><dt>Estimasi</dt><dd>{{ $rp($order->total_estimate) }} ({{ $order->days }} hari)</dd></div>
 <div class="wide"><dt>Serah terima / pengembalian</dt><dd>{{ $order->pickup_label }} · {{ $order->return_label }}</dd></div>
 @if($order->address && $order->address !== '-')<div class="wide"><dt>Alamat penyewa</dt><dd>{{ $order->address }}</dd></div>@endif
 @if($order->notes)<div class="wide"><dt>Catatan pelanggan</dt><dd>{{ $order->notes }}</dd></div>@endif</dl></div>
@endif

<form class="form-card" method="POST" action="{{ $order->exists?route('admin.orders.update',$order):route('admin.orders.store') }}">@csrf @if($order->exists) @method('PUT') @endif
<h3>Booking Order</h3>
<div class="inline-form">
 <label class="full">Booking order (nama pemesan / instansi)<input name="order_name" value="{{ old('order_name',$order->order_name ?: $order->customer_name) }}" placeholder="Bp Dwi Polda" required></label>
 <label class="full">WA pemesan (order by)<input name="phone" value="{{ old('phone', $order->phoneLabel()) }}" placeholder="08xxxxxxxxxx"></label>
 <label>Tanggal mulai<input type="date" name="start_date" id="sd" value="{{ $sd }}" required></label>
 <label>Jam mulai<input type="time" name="start_time" id="st" value="{{ $st }}" required></label>
 <label>Tanggal selesai<input type="date" name="end_date" id="ed" value="{{ $ed }}" min="{{ $sd }}" required></label>
 <label>Jam selesai<input type="time" name="end_time" id="et" value="{{ $et }}" required></label>
 <label>Unit / armada (nopol)<select name="vehicle_id" id="selV"><option value="">— pilih armada —</option>@foreach($vehicles->sortByDesc(fn($v)=>$order->car_id && $v->car_id==$order->car_id) as $v)<option value="{{ $v->id }}" data-label="{{ $v->plate_number }} · {{ $v->label }}" @selected(old('vehicle_id',$order->vehicle_id)==$v->id)>{{ $v->plate_number }} · {{ $v->label }}{{ $order->car_id && $v->car_id==$order->car_id?' ★':'' }}</option>@endforeach</select></label>
 <label>Driver<select name="driver_id" id="selD"><option value="">— tanpa supir —</option>@foreach($drivers as $d)<option value="{{ $d->id }}" data-label="{{ $d->name }} · {{ $d->phone }}" @selected(old('driver_id',$order->driver_id)==$d->id)>{{ $d->name }} · {{ $d->phone }}</option>@endforeach</select></label>
 <p class="full avail-note" id="availNote" hidden></p>
 <label>Pelayanan<input name="pelayanan" value="{{ old('pelayanan',$order->pelayanan) }}"></label>
 <label>Giat<input name="giat" value="{{ old('giat',$order->giat) }}" placeholder="TB1"></label>
 <label>Standby di<input name="standby_location" value="{{ old('standby_location',$order->standby_location) }}" placeholder="Bandara Semarang"></label>
 <label>Jam standby<input name="standby_time" value="{{ old('standby_time',$order->standby_time ?: $order->start_at->format('H.i').' WIB') }}" placeholder="12.00 WIB"></label>
 <label class="full">Tujuan<input name="destination" value="{{ old('destination',$order->destination) }}" placeholder="Magelang / mengikuti"></label>
 <label>Nama kontak tamu<input name="guest_name" value="{{ old('guest_name',$order->guest_name) }}"></label>
 <label>Telp kontak tamu<input name="guest_phone" value="{{ old('guest_phone',$order->guest_phone) }}"></label>
 <label>Status<select name="status">@foreach(\App\Models\Booking::STATUSES as $k=>$l)<option value="{{ $k }}" @selected(old('status',$order->status)===$k)>{{ $l }}</option>@endforeach</select><small>Dikonfirmasi / Berjalan mengunci jadwal armada &amp; supir.</small></label>
 <label class="full">Catatan admin (internal)<textarea name="admin_notes" rows="2">{{ old('admin_notes',$order->admin_notes) }}</textarea></label>
</div>
<div class="form-actions"><a class="btn secondary" href="{{ route('admin.orders.index') }}">Batal</a><button class="btn">Simpan Booking Order →</button></div>
@if($order->exists)<small>Setelah disimpan, laporan Booking Order bisa disalin / dibagikan ke WhatsApp klien dari halaman Daftar Booking.</small>@endif
</form>
@php $busyJson = json_encode($busy); @endphp
<script>
(function(){
 var busy={!! $busyJson !!}, g=function(i){return document.getElementById(i)};
 function overlap(b){var s=g('sd').value+'T'+(g('st').value||'00:00'),e=g('ed').value+'T'+(g('et').value||'23:59');return g('sd').value&&g('ed').value&&b.s<e&&b.e>s;}
 // Supir yang sudah ditugaskan di booking lain pada rentang tanggal ini DISEMBUNYIKAN dari pilihan (bukan sekadar dinonaktifkan).
 var drvAll=[].slice.call(g('selD').options).filter(function(o){return o.value;}).map(function(o){return {v:o.value,l:o.dataset.label};});
 var drvSel=g('selD').value;
 function filterDrivers(){var sel=g('selD'),keep=sel.value||drvSel,hidden=0,html='<option value="">— tanpa supir —</option>';
  drvAll.forEach(function(d){var hit=busy.filter(function(b){return String(b.d)===d.v&&overlap(b);})[0];if(hit){hidden++;return;}
   html+='<option value="'+d.v+'" data-label="'+d.l.replace(/"/g,'&quot;')+'">'+d.l.replace(/&/g,'&amp;').replace(/</g,'&lt;')+'</option>';});
  sel.innerHTML=html;sel.value=keep;if(sel.value!==keep)sel.value='';drvSel=sel.value;return hidden;}
 function mark(sel,key){var n=0;Array.prototype.forEach.call(sel.options,function(o){if(!o.value)return;var hit=busy.filter(function(b){return String(b[key])===o.value&&overlap(b);})[0];
  o.disabled=!!hit&&!o.selected;o.textContent=o.dataset.label+(hit?'  — sedang dipakai ('+hit.c+')':'');if(hit)n++;});return n;}
 function run(){var a=mark(g('selV'),'v'),b=filterDrivers(),note=g('availNote');
  note.hidden=false;note.textContent='Armada yang sedang dipakai booking lain tidak bisa dipilih ('+a+' armada sibuk). Supir yang sudah ditugaskan pada tanggal ini tidak ditampilkan ('+b+' supir disembunyikan).';}
 g('selD').addEventListener('change',function(){drvSel=this.value;});
 // Tanggal selesai tidak boleh sebelum tanggal mulai
 function syncEnd(){var s=g('sd').value;if(s){g('ed').min=s;if(g('ed').value&&g('ed').value<s)g('ed').value=s;}}
 g('sd').addEventListener('change',syncEnd);syncEnd();
 ['sd','st','ed','et'].forEach(function(i){g(i).addEventListener('change',run);});run();
})();
</script>
@endsection
