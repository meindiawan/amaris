@extends('admin.layout') @section('content')
<div class="page-head"><div><span>TIM LAPANGAN</span><h2>Daftar Supir</h2><p>Data supir untuk dipilih saat penugasan booking dan pencatatan keluar/masuk.</p></div><a class="btn" href="{{ route('admin.drivers.create') }}">+ Tambah Supir</a></div>
<form class="search-box" style="margin-bottom:14px" method="GET"><input name="q" value="{{ $q }}" placeholder="Cari nama atau no. WA…"><button class="btn">Cari</button></form>
<div class="table-card scroll-x"><table><thead><tr><th>Nama supir</th><th>No. WA</th><th>Foto SIM</th><th>Tugas aktif</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($drivers as $d)<tr>
<td><b>{{ $d->name }}</b></td>
<td><a href="https://wa.me/{{ $d->wa_phone }}" target="_blank" rel="noopener">{{ $d->phone_intl }}</a></td>
<td>@if($d->sim_photo)<a class="file-chip" href="{{ $d->sim_photo }}" target="_blank" rel="noopener">🪪 Lihat SIM</a>@else<small>belum ada</small>@endif</td>
<td>{{ isset($busy[$d->id]) ? $busy[$d->id]->count().' booking' : '—' }}</td>
<td><span class="status {{ $d->is_active?'on':'off' }}">{{ $d->is_active?'Aktif':'Nonaktif' }}</span></td>
<td class="actions"><a href="{{ route('admin.drivers.edit',$d) }}">Edit</a><form method="POST" action="{{ route('admin.drivers.destroy',$d) }}" onsubmit="return confirm('Hapus supir ini?')">@csrf @method('DELETE')<button>Hapus</button></form></td></tr>
@empty<tr><td colspan="6" class="empty">Belum ada supir.</td></tr>@endforelse</tbody></table></div>
@endsection
