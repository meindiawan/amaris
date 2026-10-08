@extends('admin.layout') @section('content')
<div class="page-head"><div><span>UNIT KENDARAAN</span><h2>{{ $vehicle->exists?'Edit Unit '.$vehicle->plate_number:'Tambah Unit' }}</h2><p>Kode barcode dibuat otomatis setelah disimpan.</p></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ $vehicle->exists?route('admin.vehicles.update',$vehicle):route('admin.vehicles.store') }}">@csrf @if($vehicle->exists) @method('PUT') @endif
<div class="form-grid">
<h4>Identitas unit</h4>
<label>Plat nomor<input name="plate_number" value="{{ old('plate_number',$vehicle->plate_number) }}" placeholder="H 1748 UN" required></label>
<label>Jenis mobil (katalog)<select name="car_id"><option value="">— pilih —</option>@foreach($cars as $c)<option value="{{ $c->id }}" @selected(old('car_id',$vehicle->car_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></label>
<label>Nama di invoice / booking order<input name="display_name" value="{{ old('display_name',$vehicle->display_name) }}" placeholder="Innova Reborn"><small>Kosongkan untuk memakai nama mobil katalog.</small></label>
<label>Tahun<input type="number" name="year" value="{{ old('year',$vehicle->year) }}"></label>
<label>Warna<input name="color" value="{{ old('color',$vehicle->color) }}"></label>
<label>Odometer terakhir (km)<input type="number" name="odometer" value="{{ old('odometer',$vehicle->odometer) }}"></label>
<label>No. rangka<input name="chassis_number" value="{{ old('chassis_number',$vehicle->chassis_number) }}"></label>
<label>No. mesin<input name="engine_number" value="{{ old('engine_number',$vehicle->engine_number) }}"></label>
<h4>STNK & pajak</h4>
<label>No. STNK / BPKB<input name="stnk_number" value="{{ old('stnk_number',$vehicle->stnk_number) }}"></label>
<label>Atas nama STNK<input name="stnk_owner" value="{{ old('stnk_owner',$vehicle->stnk_owner) }}"></label>
<label>Masa berlaku STNK (5 tahunan)<input type="date" name="stnk_expiry" value="{{ old('stnk_expiry',optional($vehicle->stnk_expiry)->format('Y-m-d')) }}"></label>
<label>Jatuh tempo pajak tahunan<input type="date" name="tax_expiry" value="{{ old('tax_expiry',optional($vehicle->tax_expiry)->format('Y-m-d')) }}"></label>
<label class="full">Scan / foto STNK (jpg, png, pdf)<input type="file" name="stnk_file" accept="image/*,.pdf">@if($vehicle->stnk_file)<small><a href="{{ $vehicle->stnk_file }}" target="_blank">Lihat file saat ini ↗</a></small>@endif</label>
<h4>Kepemilikan & setoran</h4>
<label>Status kepemilikan<select name="owner_type">@foreach(\App\Models\Vehicle::OWNER_TYPES as $k=>$l)<option value="{{ $k }}" @selected(old('owner_type',$vehicle->owner_type)===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Nama pemilik / mitra<input name="owner_name" value="{{ old('owner_name',$vehicle->owner_name) }}"></label>
<label>No. HP pemilik<input name="owner_phone" value="{{ old('owner_phone',$vehicle->owner_phone) }}"></label>
<label>Setoran per hari (Rp)<input type="number" name="setoran_per_day" value="{{ old('setoran_per_day',$vehicle->setoran_per_day) }}"><small>Opsional — patokan setoran harian ke pemilik.</small></label>
<label class="full">Catatan<textarea name="notes">{{ old('notes',$vehicle->notes) }}</textarea></label>
</div>
<label class="switch"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$vehicle->is_active)?'checked':'' }}> Unit aktif (bisa ditugaskan)</label>
<div class="form-actions"><a class="btn secondary" href="{{ $vehicle->exists?route('admin.vehicles.show',$vehicle):route('admin.vehicles.index') }}">Batal</a><button class="btn">Simpan Unit →</button></div></form>
@endsection
