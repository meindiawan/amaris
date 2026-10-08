@extends('admin.layout') @section('content')
<div class="page-head"><div><span>TIM LAPANGAN</span><h2>Daftar Supir</h2><p>Data supir untuk dipilih saat penugasan booking.</p></div><a class="btn" href="{{ route('admin.drivers.create') }}">+ Tambah Supir</a></div>
<form class="search-box" style="margin-bottom:14px" method="GET"><input name="q" value="{{ $q }}" placeholder="Cari nama, HP, no. SIM…"><button class="btn">Cari</button></form>
<div class="table-card"><table><thead><tr><th>Supir</th><th>No. HP</th><th>SIM</th><th>Masa berlaku SIM</th><th>Tugas aktif</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($drivers as $d)<tr>
<td><div class="table-car">@if($d->photo)<img src="{{ $d->photo }}" style="width:46px;height:46px;border-radius:50%">@endif<div><b>{{ $d->name }}</b><small>{{ \Illuminate\Support\Str::limit($d->address,40) }}</small></div></div></td>
<td><a href="https://wa.me/{{ $d->wa_phone }}" target="_blank" rel="noopener">{{ $d->phone_intl }}</a></td>
<td>{{ $d->sim_label }}</td>
<td>@if($d->sim_state)<span class="exp {{ $d->sim_state[0] }}">{{ $d->sim_state[1] }}</span>@else<small>-</small>@endif</td>
<td>{{ isset($busy[$d->id]) ? $busy[$d->id]->count().' booking' : '—' }}</td>
<td><span class="status {{ $d->is_active?'on':'off' }}">{{ $d->is_active?'Aktif':'Nonaktif' }}</span></td>
<td class="actions"><a href="{{ route('admin.drivers.edit',$d) }}">Edit</a><form method="POST" action="{{ route('admin.drivers.destroy',$d) }}" onsubmit="return confirm('Hapus supir ini?')">@csrf @method('DELETE')<button>Hapus</button></form></td></tr>
@empty<tr><td colspan="7" class="empty">Belum ada supir.</td></tr>@endforelse</tbody></table></div>
@endsection
