@extends('admin.layout') @section('content')
<div class="page-head"><div><span>ARMADA</span><h2>{{ $vehicle->exists?'Edit Armada '.$vehicle->plate_number:'Tambah Armada' }}</h2><p>Cukup isi nopol, jenis mobil, file STNK, dan file barcode BBM.</p></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $vehicle->exists?route('admin.vehicles.update',$vehicle):route('admin.vehicles.store') }}">@csrf @if($vehicle->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nopol<input name="plate_number" value="{{ old('plate_number',$vehicle->plate_number) }}" placeholder="H 1748 UN" required autocomplete="off"><small>Spasi &amp; huruf besar dirapikan otomatis.</small></label>
<label>Jenis mobil<input name="display_name" list="jenisMobil" value="{{ old('display_name',$vehicle->display_name ?: ($vehicle->car->name ?? '')) }}" placeholder="Innova Reborn" required autocomplete="off"><datalist id="jenisMobil">@foreach($cars as $c)<option value="{{ $c->name }}">@endforeach</datalist><small>Ketik bebas atau pilih dari katalog website.</small></label>
<label class="full">File STNK (jpg, png, pdf)<input type="file" name="stnk_file" accept="image/*,.pdf">@if($vehicle->stnk_file)<small><a href="{{ $vehicle->stnk_file }}" target="_blank" rel="noopener">Lihat file STNK saat ini ↗</a> — pilih file baru untuk mengganti.</small>@endif</label>
<label class="full">File barcode BBM (jpg, png, pdf)<input type="file" name="barcode_image" accept="image/*,.pdf">@if($vehicle->barcode_image)<small><a href="{{ $vehicle->barcode_image }}" target="_blank" rel="noopener">Lihat file barcode BBM saat ini ↗</a> — pilih file baru untuk mengganti.</small>@endif</label>
</div>
<label class="switch"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$vehicle->is_active)?'checked':'' }}> Armada aktif (bisa dipilih di booking & keluar/masuk)</label>
<div class="form-actions"><a class="btn secondary" href="{{ $vehicle->exists?route('admin.vehicles.show',$vehicle):route('admin.vehicles.index') }}">Batal</a><button class="btn">Simpan Armada →</button></div></form>
@endsection
