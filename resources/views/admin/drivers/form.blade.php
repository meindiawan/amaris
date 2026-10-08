@extends('admin.layout') @section('content')
<div class="page-head"><div><span>SUPIR</span><h2>{{ $driver->exists?'Edit Supir':'Tambah Supir' }}</h2></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $driver->exists?route('admin.drivers.update',$driver):route('admin.drivers.store') }}">@csrf @if($driver->exists) @method('PUT') @endif
<div class="form-grid">
<label>Nama supir<input name="name" value="{{ old('name',$driver->name) }}" required></label>
<label>No. HP / WhatsApp<input name="phone" value="{{ old('phone',$driver->phone) }}" placeholder="0821-3621-562" required></label>
<label>No. HP darurat (keluarga)<input name="emergency_phone" value="{{ old('emergency_phone',$driver->emergency_phone) }}"></label>
<label>NIK (KTP)<input name="nik" value="{{ old('nik',$driver->nik) }}"></label>
<label class="full">Alamat<textarea name="address">{{ old('address',$driver->address) }}</textarea></label>
<label>No. SIM<input name="sim_number" value="{{ old('sim_number',$driver->sim_number) }}"></label>
<label>Golongan SIM<select name="sim_type"><option value="">—</option>@foreach(\App\Models\Driver::SIM_TYPES as $k=>$l)<option value="{{ $k }}" @selected(old('sim_type',$driver->sim_type)===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Masa berlaku SIM<input type="date" name="sim_expiry" value="{{ old('sim_expiry',optional($driver->sim_expiry)->format('Y-m-d')) }}"></label>
<label>Foto supir<input type="file" name="photo" accept="image/*">@if($driver->photo)<img class="preview" src="{{ $driver->photo }}">@endif</label>
<label class="full">Catatan<textarea name="notes">{{ old('notes',$driver->notes) }}</textarea></label>
</div>
<label class="switch"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$driver->is_active)?'checked':'' }}> Supir aktif</label>
<div class="form-actions"><a class="btn secondary" href="{{ route('admin.drivers.index') }}">Batal</a><button class="btn">Simpan Supir →</button></div></form>
@endsection
