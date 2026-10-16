@php
 $vOut = \App\Models\Vehicle::where('is_out',true)->count(); $vAll = \App\Models\Vehicle::where('is_active',true)->count();
 $unpaid = \App\Models\VehicleLog::where('payment_status','belum')->count();
 $noInv = \App\Models\VehicleLog::where('invoice_status','!=','sudah_terkirim')->count();
 $needAssign = \App\Models\Booking::whereIn('status',['pending','confirmed'])->where('end_at','>=',now())->get()->filter(fn($b)=>!$b->is_assigned)->count();
 $readyVehicles = \App\Models\Vehicle::readyNow();
 $dAll = \App\Models\Driver::where('is_active',true)->count(); $readyDrivers = \App\Models\Driver::readyNow();
 $latestLogs = \App\Models\VehicleLog::with('driver')->orderByDesc('out_date')->orderByDesc('id')->take(6)->get();
@endphp
<div class="stats"><div class="ready"><small>Unit Ready</small><strong>{{ $readyVehicles->count() }}<span style="display:inline;font-size:16px"> / {{ $vAll }}</span></strong><span>siap disewakan sekarang</span></div><div class="ready"><small>Driver Ready</small><strong>{{ $readyDrivers->count() }}<span style="display:inline;font-size:16px"> / {{ $dAll }}</span></strong><span>belum ditugaskan</span></div><div><small>Unit Keluar</small><strong>{{ $vOut }}<span style="display:inline;font-size:16px"> / {{ $vAll }}</span></strong><span><a href="{{ route('admin.movements.index') }}">papan garasi →</a></span></div><div><small>Belum Ditugaskan</small><strong>{{ $needAssign }}</strong><span>booking butuh unit / supir</span></div><div><small>Payment Belum Masuk</small><strong>{{ $unpaid }}</strong><span><a href="{{ route('admin.movements.index',['f'=>'unpaid']) }}">lihat catatan →</a></span></div><div><small>Invoice Belum Terkirim</small><strong>{{ $noInv }}</strong><span><a href="{{ route('admin.movements.index',['f'=>'noinvoice']) }}">lihat catatan →</a></span></div></div>
<div class="dash-cols">
 <div class="table-card dash-panel"><div class="recent-head"><h3>Unit Ready</h3><a href="{{ route('admin.vehicles.index') }}">Armada →</a></div>
  <div class="chip-list">@forelse($readyVehicles as $v)<a class="ready-chip" href="{{ route('admin.vehicles.show',$v) }}"><span class="plate">{{ $v->plate_nb }}</span><small>{{ $v->label }}</small></a>@empty<p class="empty">Tidak ada unit yang ready saat ini.</p>@endforelse</div></div>
 <div class="table-card dash-panel"><div class="recent-head"><h3>Driver Ready</h3><a href="{{ route('admin.drivers.index') }}">Daftar supir →</a></div>
  <div class="chip-list">@forelse($readyDrivers as $d)<a class="ready-chip" href="{{ route('admin.drivers.edit',$d) }}"><b>{{ $d->name }}</b><small>{{ $d->phone }}</small></a>@empty<p class="empty">Tidak ada driver yang ready saat ini.</p>@endforelse</div></div>
</div>
<div class="table-card recent-card"><div class="recent-head"><h3>Mobil Keluar / Masuk Terbaru</h3><a href="{{ route('admin.movements.index') }}">Lihat semua →</a></div>
 <div class="scroll-x"><table><thead><tr><th>Nopol</th><th>Jenis</th><th>Tgl keluar</th><th>Tgl masuk</th><th>Driver</th><th>Status</th></tr></thead><tbody>
 @forelse($latestLogs as $m)<tr onclick="location='{{ route('admin.movements.edit',$m) }}'" style="cursor:pointer"><td class="plate-cell"><span class="plate">{{ $m->plate_nb }}</span></td><td>{{ $m->vehicle_type ?: '-' }}</td><td>{{ $m->out_date->format('d M Y') }}</td><td>{{ $m->in_date ? $m->in_date->format('d M Y') : '-' }}</td><td>{{ $m->driver_name ?: '-' }}</td><td>@if($m->in_date)<span class="status in">Sudah masuk</span>@else<span class="status out">Sedang keluar</span>@endif</td></tr>
 @empty<tr><td colspan="6" class="empty">Belum ada catatan mobil keluar/masuk.</td></tr>@endforelse</tbody></table></div></div>
