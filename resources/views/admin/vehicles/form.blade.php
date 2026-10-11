@extends('admin.layout') @section('content')
<div class="page-head"><div><span>UNIT KENDARAAN</span><h2>{{ $vehicle->exists?'Edit Unit '.$vehicle->plate_number:'Tambah Unit' }}</h2><p>Kode barcode dibuat otomatis setelah disimpan.</p></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $vehicle->exists?route('admin.vehicles.update',$vehicle):route('admin.vehicles.store') }}">@csrf @if($vehicle->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nopol<input name="plate_number" value="{{ old('plate_number',$vehicle->plate_number) }}" placeholder="H 1748 UN" required></label>
<label>Jenis mobil<select name="car_id"><option value="">— pilih jenis mobil —</option>@foreach($cars as $c)<option value="{{ $c->id }}" @selected(old('car_id',$vehicle->car_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></label>
<label class="full">Input file STNK<input type="file" name="stnk_file" accept="image/*,.pdf">@if($vehicle->stnk_file)<small><a href="{{ $vehicle->stnk_file }}" target="_blank">Lihat file STNK saat ini ↗</a></small>@endif</label>
<label class="full">Input file barcode BBM<input type="file" name="fuel_barcode_file" accept="image/*,.pdf">@if($vehicle->fuel_barcode_file)<small><a href="{{ $vehicle->fuel_barcode_file }}" target="_blank">Lihat file barcode BBM ↗</a></small>@endif</label>
</div>

<input type="hidden" name="is_active" value="1">
<div class="form-actions"><a class="btn secondary" href="{{ $vehicle->exists?route('admin.vehicles.show',$vehicle):route('admin.vehicles.index') }}">Batal</a><button class="btn">Simpan Unit →</button></div></form>
@endsection
