@extends('admin.layout') @section('content')
<div class="page-head"><div><span>ARMADA FISIK</span><h2>Armada (Nopol)</h2><p>Daftar mobil berdasarkan nopol beserta file STNK dan barcode BBM.</p></div>
<div class="head-actions"><a class="btn secondary" href="{{ route('admin.vehicles.labels') }}" target="_blank">Cetak Semua Barcode</a><a class="btn" href="{{ route('admin.vehicles.create') }}">+ Tambah Armada</a></div></div>
<form class="search-box" style="margin-bottom:14px" method="GET">@if($f)<input type="hidden" name="f" value="{{ $f }}">@endif<input name="q" value="{{ $q }}" placeholder="Cari nopol atau jenis mobil…"><button class="btn">Cari</button></form>
<div class="filter-tabs"><a href="{{ route('admin.vehicles.index',['q'=>$q?:null]) }}" class="{{ !$f?'on':'' }}">Semua</a><a href="{{ route('admin.vehicles.index',['f'=>'in','q'=>$q?:null]) }}" class="{{ $f==='in'?'on':'' }}">Di garasi</a><a href="{{ route('admin.vehicles.index',['f'=>'out','q'=>$q?:null]) }}" class="{{ $f==='out'?'on':'' }}">Sedang keluar</a><a href="{{ route('admin.vehicles.index',['f'=>'off','q'=>$q?:null]) }}" class="{{ $f==='off'?'on':'' }}">Nonaktif</a></div>
<div class="table-card scroll-x"><table><thead><tr><th>Nopol</th><th>Jenis mobil</th><th>File STNK</th><th>Barcode BBM</th><th>Lokasi</th><th></th></tr></thead><tbody>
@forelse($vehicles as $v)<tr>
<td><span class="plate">{{ $v->plate_number }}</span></td>
<td><b>{{ $v->label }}</b></td>
<td>@if($v->stnk_file)<a class="file-chip" href="{{ $v->stnk_file }}" target="_blank" rel="noopener">📄 Lihat STNK</a>@else<small>belum ada</small>@endif</td>
<td>@if($v->barcode_image)<a class="file-chip" href="{{ $v->barcode_image }}" target="_blank" rel="noopener">▥ Lihat barcode</a>@else<small>belum ada</small>@endif</td>
<td>@if(!$v->is_active)<span class="status off">Nonaktif</span>@elseif($v->is_out)<span class="status out">Sedang keluar</span>@else<span class="status in">Di garasi</span>@endif</td>
<td class="actions"><a href="{{ route('admin.vehicles.show',$v) }}">Detail</a><a href="{{ route('admin.vehicles.edit',$v) }}">Edit</a></td></tr>
@empty<tr><td colspan="6" class="empty">Belum ada armada. Klik "+ Tambah Armada".</td></tr>@endforelse</tbody></table></div>
@endsection
