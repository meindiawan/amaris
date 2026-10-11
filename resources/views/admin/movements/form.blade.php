@extends('admin.layout') @section('content')
<div class="page-head"><div><span>CATAT PERJALANAN</span><h2>{{ $log->exists?'Edit Catatan '.$log->plate_number:'Mobil Keluar / Masuk' }}</h2><p>Pilih nopol dari daftar. Jika tidak ada, pilih <b>Lainnya</b> lalu isi nopol, jenis kendaraan, dan rental.</p></div><a class="btn secondary" href="{{ route('admin.movements.index') }}">← Kembali</a></div>
<form class="form-card" method="POST" action="{{ $log->exists?route('admin.movements.update',$log):route('admin.movements.store') }}">@csrf @if($log->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nopol<select name="vehicle_key" id="vkey" required><option value="">— pilih nopol —</option>@foreach($vehicles as $v)<option value="{{ $v->id }}" @selected((string)$vehicleKey===(string)$v->id)>{{ $v->plate_number }} · {{ $v->label }}</option>@endforeach<option value="other" @selected($vehicleKey==='other')>Lainnya (nopol tidak ada di daftar)</option></select></label>
<div class="full other-box" id="otherVeh" hidden><div class="form-grid" style="margin:0">
<label>Nopol<input name="plate_number" value="{{ old('plate_number',$vehicleKey==='other'?$log->plate_number:'') }}" placeholder="H 1234 AB" autocomplete="off"></label>
<label>Jenis kendaraan<input name="vehicle_type" value="{{ old('vehicle_type',$vehicleKey==='other'?$log->vehicle_type:'') }}" placeholder="Innova Zenix"></label>
<label class="full">Rental<input name="rental_name" value="{{ old('rental_name',$vehicleKey==='other'?$log->rental_name:'') }}" placeholder="Nama rental pemilik mobil"></label></div></div>
<label>Tanggal keluar<input type="date" name="out_date" value="{{ old('out_date',optional($log->out_date)->format('Y-m-d')) }}" required></label>
<label>Tanggal masuk<input type="date" name="in_date" value="{{ old('in_date',optional($log->in_date)->format('Y-m-d')) }}"><small>Kosongkan bila mobil belum kembali.</small></label>
<label class="full">User yang booking<input name="booker_name" list="bookers" value="{{ old('booker_name',$log->booker_name) }}" placeholder="Nama pemesan" autocomplete="off"><datalist id="bookers">@foreach($bookers as $n)<option value="{{ $n }}">@endforeach</datalist></label>
<label>Invoice<select name="invoice_status">@foreach(\App\Models\VehicleLog::INVOICE as $k=>$l)<option value="{{ $k }}" @selected(old('invoice_status',$log->invoice_status)===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Payment<select name="payment_status" id="pstat">@foreach(\App\Models\VehicleLog::PAYMENT as $k=>$l)<option value="{{ $k }}" @selected(old('payment_status',$log->payment_status)===$k)>{{ $l }}</option>@endforeach</select></label>
<label class="full" id="pnoteBox" hidden>Catatan payment — diterima oleh siapa?<input name="payment_note" value="{{ old('payment_note',$log->payment_note) }}" placeholder="Mis. Bu Damaris / transfer ke rekening BCA"></label>
<label>Driver<select name="driver_key" id="dkey"><option value="">— tanpa driver —</option>@foreach($drivers as $d)<option value="{{ $d->id }}" @selected((string)$driverKey===(string)$d->id)>{{ $d->name }}</option>@endforeach<option value="other" @selected($driverKey==='other')>Lainnya (driver tidak ada di daftar)</option></select></label>
<label id="dnameBox" hidden>Nama driver<input name="driver_name" value="{{ old('driver_name',$driverKey==='other'?$log->driver_name:'') }}" placeholder="Nama driver"></label>
<label class="full">Keterangan<textarea name="notes" rows="3">{{ old('notes',$log->notes) }}</textarea></label>
</div>
<div class="form-actions"><a class="btn secondary" href="{{ route('admin.movements.index') }}">Batal</a><button class="btn">Simpan Catatan →</button></div></form>
<script>
(function(){function t(){document.getElementById('otherVeh').hidden=document.getElementById('vkey').value!=='other';
document.getElementById('pnoteBox').hidden=document.getElementById('pstat').value!=='sudah';
document.getElementById('dnameBox').hidden=document.getElementById('dkey').value!=='other';}
['vkey','pstat','dkey'].forEach(function(i){document.getElementById(i).addEventListener('change',t);});t();})();
</script>
@endsection
