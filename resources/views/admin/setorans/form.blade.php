@extends('admin.layout') @section('content')
<div class="page-head"><div><span>KEUANGAN</span><h2>{{ $row->exists?'Edit Setoran':'Catat Setoran' }}</h2></div></div>
<form class="form-card" method="POST" action="{{ $row->exists?route('admin.setoran.update',$row):route('admin.setoran.store') }}">@csrf @if($row->exists) @method('PUT') @endif
<div class="form-grid">
<label>Jenis<select name="kind">@foreach(\App\Models\Setoran::KINDS as $k=>$l)<option value="{{ $k }}" @selected(old('kind',$row->kind)===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Jumlah (Rp)<input type="number" name="amount" min="1" value="{{ old('amount',$row->amount) }}" required></label>
<label>Tanggal<input type="date" name="paid_at" value="{{ old('paid_at',optional($row->paid_at)->format('Y-m-d')) }}" required></label>
<label>Metode<select name="method">@foreach(\App\Models\Setoran::METHODS as $k=>$l)<option value="{{ $k }}" @selected(old('method',$row->method)===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Unit<select name="vehicle_id"><option value="">—</option>@foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(old('vehicle_id',$row->vehicle_id)==$v->id)>{{ $v->plate_number }} · {{ $v->label }}</option>@endforeach</select></label>
<label>Supir<select name="driver_id"><option value="">—</option>@foreach($drivers as $d)<option value="{{ $d->id }}" @selected(old('driver_id',$row->driver_id)==$d->id)>{{ $d->name }}</option>@endforeach</select></label>
<label class="full">Booking terkait (opsional)<select name="booking_id"><option value="">—</option>@foreach($bookings as $b)<option value="{{ $b->id }}" @selected(old('booking_id',$row->booking_id)==$b->id)>{{ $b->code }} · {{ $b->customer_name }}</option>@endforeach</select></label>
<label class="full">Keterangan<textarea name="note">{{ old('note',$row->note) }}</textarea></label>
</div><div class="form-actions"><a class="btn secondary" href="{{ route('admin.setoran.index') }}">Batal</a><button class="btn">Simpan →</button></div></form>
@endsection
