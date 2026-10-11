@extends('admin.layout') @section('content')
<div class="page-head"><div><span>SUPIR</span><h2>{{ $driver->exists?'Edit Supir':'Tambah Supir' }}</h2></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $driver->exists?route('admin.drivers.update',$driver):route('admin.drivers.store') }}">@csrf @if($driver->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nama supir<input name="name" value="{{ old('name',$driver->name) }}" required></label>
<label>No. WhatsApp<input name="phone" value="{{ old('phone',$driver->phone) }}" placeholder="0821-3621-562" required></label>
<label class="full">Input file foto SIM<input type="file" name="sim_photo" accept="image/*">@if($driver->sim_photo)<small>File SIM saat ini:</small><img class="preview" src="{{ $driver->sim_photo }}" alt="Foto SIM">@endif</label>
</div>

<input type="hidden" name="is_active" value="1">
<div class="form-actions"><a class="btn secondary" href="{{ route('admin.drivers.index') }}">Batal</a><button class="btn">Simpan Supir →</button></div></form>
@endsection
