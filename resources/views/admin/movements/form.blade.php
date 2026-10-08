@extends('admin.layout') @section('content')
<div class="page-head"><div><span>CATAT PERGERAKAN</span><h2><span class="plate">{{ $vehicle->plate_number }}</span> {{ $vehicle->label }}</h2><p>{{ $vehicle->is_out?'Unit sedang di luar — catat kedatangan (masuk).':'Unit di garasi — catat keberangkatan (keluar).' }}</p></div><a class="btn secondary" href="{{ route('admin.movements.index') }}">← Kembali</a></div>
<form class="form-card" method="POST" action="{{ route('admin.movements.store') }}">@csrf<input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
<div class="form-grid">
<label>Jenis<select name="type"><option value="keluar" @selected(old('type',$type)==='keluar')>Keluar (berangkat)</option><option value="masuk" @selected(old('type',$type)==='masuk')>Masuk (kembali)</option></select></label>
<label>Waktu<input type="datetime-local" name="moved_at" value="{{ old('moved_at',now()->format('Y-m-d\TH:i')) }}" required></label>
<label>Booking<select name="booking_id" id="bk"><option value="">— tanpa booking —</option>@foreach($bookings as $b)<option value="{{ $b->id }}" data-driver="{{ $b->driver_id }}" data-dest="{{ $b->destination }}" @selected(old('booking_id')==$b->id)>{{ $b->code }} · {{ $b->customer_name }} · {{ $b->start_at->format('d M') }}</option>@endforeach</select></label>
<label>Supir<select name="driver_id" id="drv"><option value="">—</option>@foreach($drivers as $d)<option value="{{ $d->id }}" @selected(old('driver_id')==$d->id)>{{ $d->name }}</option>@endforeach</select></label>
<label>Odometer (km)<input type="number" name="odometer" value="{{ old('odometer',$vehicle->odometer) }}"><small>@if($lastOut && $lastOut->odometer)Saat keluar terakhir: {{ $lastOut->odometer }} km.@endif</small></label>
<label>Level BBM<select name="fuel"><option value="">—</option>@foreach(\App\Models\Vehicle::FUEL as $k=>$l)<option value="{{ $k }}" @selected(old('fuel')===$k)>{{ $l }}</option>@endforeach</select></label>
<label class="full">Tujuan<input name="destination" id="dst" value="{{ old('destination') }}"></label>
<label class="full">Kondisi / kelengkapan / kerusakan<textarea name="condition_notes" placeholder="Lecet, kelengkapan (STNK, dongkrak, kunci cadangan), kebersihan…">{{ old('condition_notes') }}</textarea></label>
</div>
<label class="switch"><input type="checkbox" name="update_booking" value="1" checked> Ubah status booking otomatis (keluar → Sedang Berjalan, masuk → Selesai)</label>
<div class="form-actions"><button class="btn">Simpan Catatan →</button></div></form>
<script>document.getElementById('bk').onchange=function(){var o=this.selectedOptions[0];if(o.dataset.driver)document.getElementById('drv').value=o.dataset.driver;if(o.dataset.dest&&!document.getElementById('dst').value)document.getElementById('dst').value=o.dataset.dest;};</script>
@endsection
