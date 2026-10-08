@extends('admin.layout') @section('content')
<div class="page-head"><div><span>{{ $vehicle->barcode }}</span><h2><span class="plate">{{ $vehicle->plate_number }}</span> {{ $vehicle->label }}</h2><p>@if($vehicle->is_out)<span class="status out">Sedang keluar</span> sejak {{ optional($vehicle->last_moved_at)->format('d M Y H:i') }}@else<span class="status in">Di garasi</span>@endif</p></div>
<div class="head-actions"><a class="btn" href="{{ route('admin.movements.create',['vehicle'=>$vehicle->id]) }}">Catat {{ $vehicle->is_out?'Masuk':'Keluar' }}</a><a class="btn secondary" href="{{ route('admin.vehicles.edit',$vehicle) }}">Edit</a><a class="btn secondary" href="{{ route('admin.vehicles.index') }}">← Daftar</a></div></div>
<div class="detail-cols"><div class="detail-main">
<div class="form-card"><h3>Data Unit</h3><dl class="dl">
<div><dt>Jenis</dt><dd>{{ $vehicle->car->name ?? '-' }}</dd></div><div><dt>Tahun / warna</dt><dd>{{ $vehicle->year ?: '-' }} / {{ $vehicle->color ?: '-' }}</dd></div>
<div><dt>No. rangka</dt><dd>{{ $vehicle->chassis_number ?: '-' }}</dd></div><div><dt>No. mesin</dt><dd>{{ $vehicle->engine_number ?: '-' }}</dd></div>
<div><dt>No. STNK</dt><dd>{{ $vehicle->stnk_number ?: '-' }}</dd></div><div><dt>Atas nama</dt><dd>{{ $vehicle->stnk_owner ?: '-' }}</dd></div>
<div><dt>Masa berlaku STNK</dt><dd>@if($vehicle->stnk_state)<span class="exp {{ $vehicle->stnk_state[0] }}">{{ $vehicle->stnk_state[1] }}</span>@else - @endif</dd></div>
<div><dt>Pajak tahunan</dt><dd>@if($vehicle->tax_state)<span class="exp {{ $vehicle->tax_state[0] }}">{{ $vehicle->tax_state[1] }}</span>@else - @endif</dd></div>
<div><dt>Pemilik</dt><dd>{{ \App\Models\Vehicle::OWNER_TYPES[$vehicle->owner_type] }} {{ $vehicle->owner_name?'· '.$vehicle->owner_name:'' }}</dd></div><div><dt>Odometer</dt><dd>{{ $vehicle->odometer!==null?number_format($vehicle->odometer,0,',','.').' km':'-' }}</dd></div>
@if($vehicle->notes)<div class="wide"><dt>Catatan</dt><dd>{{ $vehicle->notes }}</dd></div>@endif</dl>
@if($vehicle->stnk_file)<p><a class="btn secondary" href="{{ $vehicle->stnk_file }}" target="_blank">Lihat scan STNK ↗</a></p>@endif</div>
<div class="form-card"><h3>Riwayat Keluar / Masuk</h3><table class="mini-table"><thead><tr><th>Waktu</th><th>Jenis</th><th>Supir</th><th>KM</th><th>BBM</th><th>Catatan</th></tr></thead><tbody>
@forelse($moves as $m)<tr><td>{{ $m->moved_at->format('d M Y H:i') }}</td><td><span class="status {{ $m->type==='keluar'?'out':'in' }}">{{ $m->type==='keluar'?'Keluar':'Masuk' }}</span></td><td>{{ $m->driver->name ?? '-' }}</td><td>{{ $m->odometer ?? '-' }}</td><td>{{ $m->fuel ?? '-' }}</td><td>{{ $m->condition_notes }}</td></tr>@empty<tr><td colspan="6" class="empty">Belum ada catatan.</td></tr>@endforelse</tbody></table></div>
<div class="form-card"><h3>Booking Terakhir</h3><table class="mini-table"><tbody>@forelse($bookings as $b)<tr onclick="location='{{ route('admin.bookings.show',$b) }}'" style="cursor:pointer"><td><b>{{ $b->code }}</b></td><td>{{ $b->customer_name }}</td><td>{{ $b->start_at->format('d M') }} – {{ $b->end_at->format('d M Y') }}</td><td><span class="status st-{{ $b->status }}">{{ $b->status_label }}</span></td></tr>@empty<tr><td class="empty">Belum pernah ditugaskan.</td></tr>@endforelse</tbody></table></div>
</div><aside class="detail-side">
<div class="form-card barcode-box"><h3>Barcode Unit</h3>{!! $vehicle->barcode_svg !!}<code>{{ $vehicle->barcode }}</code><p><a class="btn" target="_blank" href="{{ route('admin.vehicles.label',$vehicle) }}">Cetak Label</a></p><small>Tempel di unit / kunci. Scan di menu <b>Keluar Masuk</b>.</small></div>
<div class="form-card price-card"><h3>Setoran</h3><div class="row total"><span>Total tercatat</span><b>Rp {{ number_format($setoranTotal,0,',','.') }}</b></div><a class="btn secondary" style="margin-top:12px" href="{{ route('admin.setoran.create',['vehicle'=>$vehicle->id]) }}">+ Catat Setoran</a></div>
<form method="POST" action="{{ route('admin.vehicles.destroy',$vehicle) }}" onsubmit="return confirm('Hapus unit ini?')">@csrf @method('DELETE')<button class="link-danger">Hapus unit</button></form>
</aside></div>
@endsection
