@extends('admin.layout') @section('content')
<div class="page-head"><div><span>ARMADA FISIK</span><h2>Unit Kendaraan</h2><p>Setiap unit punya plat, STNK, dan kode barcode sendiri.</p></div>
<div class="head-actions"><a class="btn secondary" href="{{ route('admin.vehicles.labels') }}" target="_blank">Cetak Semua Barcode</a><a class="btn" href="{{ route('admin.vehicles.create') }}">+ Tambah Unit</a></div></div>
<form class="search-box" style="margin-bottom:14px" method="GET">@if($f)<input type="hidden" name="f" value="{{ $f }}">@endif<input name="q" value="{{ $q }}" placeholder="Cari plat, nama, barcode, pemilik…"><button class="btn">Cari</button></form>
<div class="filter-tabs"><a href="{{ route('admin.vehicles.index',['q'=>$q?:null]) }}" class="{{ !$f?'on':'' }}">Semua</a><a href="{{ route('admin.vehicles.index',['f'=>'in','q'=>$q?:null]) }}" class="{{ $f==='in'?'on':'' }}">Di garasi</a><a href="{{ route('admin.vehicles.index',['f'=>'out','q'=>$q?:null]) }}" class="{{ $f==='out'?'on':'' }}">Sedang keluar</a><a href="{{ route('admin.vehicles.index',['f'=>'off','q'=>$q?:null]) }}" class="{{ $f==='off'?'on':'' }}">Nonaktif</a></div>
<div class="table-card"><table><thead><tr><th>Unit</th><th>STNK</th><th>Pajak</th><th>Pemilik</th><th>Lokasi</th><th>Barcode</th><th></th></tr></thead><tbody>
@forelse($vehicles as $v)<tr>
<td><span class="plate">{{ $v->plate_number }}</span><small>{{ $v->label }}{{ $v->year?' · '.$v->year:'' }}</small></td>
<td>@if($v->stnk_state)<span class="exp {{ $v->stnk_state[0] }}">{{ $v->stnk_state[1] }}</span>@else<small>-</small>@endif</td>
<td>@if($v->tax_state)<span class="exp {{ $v->tax_state[0] }}">{{ $v->tax_state[1] }}</span>@else<small>-</small>@endif</td>
<td>{{ \App\Models\Vehicle::OWNER_TYPES[$v->owner_type] }}<small>{{ $v->owner_name }}</small></td>
<td>@if(!$v->is_active)<span class="status off">Nonaktif</span>@elseif($v->is_out)<span class="status out">Sedang keluar</span>@else<span class="status in">Di garasi</span>@endif</td>
<td><b>{{ $v->barcode }}</b></td>
<td class="actions"><a href="{{ route('admin.vehicles.show',$v) }}">Detail</a><a href="{{ route('admin.vehicles.edit',$v) }}">Edit</a></td></tr>
@empty<tr><td colspan="7" class="empty">Belum ada unit. Klik "+ Tambah Unit".</td></tr>@endforelse</tbody></table></div>
@endsection
