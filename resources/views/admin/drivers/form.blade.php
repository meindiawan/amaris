@extends('admin.layout') @section('content')
<div class="page-head"><div><span>SUPIR</span><h2>{{ $driver->exists?'Edit Supir':'Tambah Supir' }}</h2><p>Cukup isi nama supir, nomor WA, dan foto SIM.</p></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $driver->exists?route('admin.drivers.update',$driver):route('admin.drivers.store') }}">@csrf @if($driver->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nama supir<input name="name" value="{{ old('name',$driver->name) }}" required></label>
<label>No. WA<input name="phone" value="{{ old('phone',$driver->phone) }}" placeholder="0821-3621-562" required inputmode="tel"></label>
<label class="full">Foto SIM (jpg, png, pdf)<input type="file" name="sim_photo" accept="image/*,.pdf">@if($driver->sim_photo)<small><a href="{{ $driver->sim_photo }}" target="_blank" rel="noopener">Lihat foto SIM saat ini ↗</a> — pilih file baru untuk mengganti.</small>@endif</label>
</div>
<label class="switch"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$driver->is_active)?'checked':'' }}> Supir aktif (bisa dipilih di booking & keluar/masuk)</label>
<div class="form-actions"><a class="btn secondary" href="{{ route('admin.drivers.index') }}">Batal</a><button class="btn">Simpan Supir →</button></div></form>
@endsection
