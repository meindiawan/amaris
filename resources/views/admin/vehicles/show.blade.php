@extends('admin.layout') @section('content')
<div class="page-head"><div><span>{{ $vehicle->barcode }}</span><h2><span class="plate">{{ $vehicle->plate_number }}</span> {{ $vehicle->label }}</h2><p>@if($vehicle->is_out)<span class="status out">Sedang keluar</span> sejak {{ optional($vehicle->last_moved_at)->format('d M Y') }}@else<span class="status in">Di garasi</span>@endif</p></div>
<div class="head-actions"><a class="btn" href="{{ route('admin.movements.create',['vehicle'=>$vehicle->id]) }}">Catat Keluar / Masuk</a><a class="btn secondary" href="{{ route('admin.vehicles.edit',$vehicle) }}">Edit</a><a class="btn secondary" href="{{ route('admin.vehicles.index') }}">← Daftar</a></div></div>
<div class="detail-cols"><div class="detail-main">
<div class="form-card"><h3>Data Armada</h3><dl class="dl">
<div><dt>Nopol</dt><dd>{{ $vehicle->plate_number }}</dd></div><div><dt>Jenis mobil</dt><dd>{{ $vehicle->label }}</dd></div>
<div><dt>File STNK</dt><dd>@if($vehicle->stnk_file)<a href="{{ $vehicle->stnk_file }}" target="_blank" rel="noopener">Lihat STNK ↗</a>@else - @endif</dd></div>
<div><dt>File barcode BBM</dt><dd>@if($vehicle->barcode_image)<a href="{{ $vehicle->barcode_image }}" target="_blank" rel="noopener">Lihat barcode BBM ↗</a>@else - @endif</dd></div></dl>
@if($vehicle->barcode_image && !\Illuminate\Support\Str::endsWith(strtolower($vehicle->barcode_image),'.pdf'))<p><img src="{{ $vehicle->barcode_image }}" alt="Barcode BBM" style="max-width:260px;border:1px solid var(--line);border-radius:12px;background:#fff;padding:8px"></p>@endif</div>
<div class="form-card"><h3>Riwayat Keluar / Masuk</h3><table class="mini-table"><thead><tr><th>Keluar</th><th>Masuk</th><th>Booking</th><th>Driver</th><th>Keterangan</th></tr></thead><tbody>
@forelse($logs as $m)<tr><td>{{ $m->out_date->format('d M Y') }}</td><td>@if($m->in_date){{ $m->in_date->format('d M Y') }}@else<span class="status out">belum masuk</span>@endif</td><td>{{ $m->booker_name ?: '-' }}</td><td>{{ $m->driver_name ?: '-' }}</td><td>{{ $m->notes }}</td></tr>@empty<tr><td colspan="5" class="empty">Belum ada catatan.</td></tr>@endforelse</tbody></table></div>
<div class="form-card"><h3>Booking Terakhir</h3><table class="mini-table"><tbody>@forelse($bookings as $b)<tr onclick="location='{{ route('admin.orders.edit',$b) }}'" style="cursor:pointer"><td><b>{{ $b->code }}</b></td><td>{{ $b->order_name ?: $b->customer_name }}</td><td>{{ $b->start_at->format('d M') }} – {{ $b->end_at->format('d M Y') }}</td><td><span class="status st-{{ $b->status }}">{{ $b->status_label }}</span></td></tr>@empty<tr><td class="empty">Belum pernah ditugaskan.</td></tr>@endforelse</tbody></table></div>
</div><aside class="detail-side">
<div class="form-card barcode-box"><h3>Kode Unit</h3>{!! $vehicle->barcode_svg !!}<code>{{ $vehicle->barcode }}</code><p><a class="btn" target="_blank" href="{{ route('admin.vehicles.label',$vehicle) }}">Cetak Label</a></p><small>Tempel di unit / kunci. Scan di menu <b>Keluar Masuk</b>.</small></div>
<form method="POST" action="{{ route('admin.vehicles.destroy',$vehicle) }}" onsubmit="return confirm('Hapus armada ini?')">@csrf @method('DELETE')<button class="link-danger">Hapus armada</button></form>
</aside></div>
@endsection
