@php
 $vOut = \App\Models\Vehicle::where('is_out',true)->count(); $vAll = \App\Models\Vehicle::where('is_active',true)->count();
 $soon = now()->addDays(30)->toDateString();
 $warnV = \App\Models\Vehicle::where('is_active',true)->where(fn($q)=>$q->whereDate('stnk_expiry','<=',$soon)->orWhereDate('tax_expiry','<=',$soon))->orderBy('plate_number')->get();
 $warnD = \App\Models\Driver::where('is_active',true)->whereDate('sim_expiry','<=',$soon)->orderBy('name')->get();
 $unpaid = \App\Models\Booking::whereIn('status',['confirmed','ongoing','completed'])->with(['payments','invoice.items'])->get()->filter(fn($b)=>$b->balance_due>0)->count();
 $needAssign = \App\Models\Booking::whereIn('status',['pending','confirmed'])->where('end_at','>=',now())->get()->filter(fn($b)=>!$b->is_assigned)->count();
@endphp
<div class="stats"><div><small>Unit Keluar</small><strong>{{ $vOut }}<span style="display:inline;font-size:16px"> / {{ $vAll }}</span></strong><span><a href="{{ route('admin.movements.index') }}">papan garasi →</a></span></div><div><small>Belum Ditugaskan</small><strong>{{ $needAssign }}</strong><span>booking butuh unit / supir</span></div><div><small>Belum Lunas</small><strong>{{ $unpaid }}</strong><span><a href="{{ route('admin.invoices.index') }}">lihat invoice →</a></span></div></div>
@if($warnV->count() || $warnD->count())<div class="table-card recent-card"><div class="recent-head"><h3>⚠ Dokumen Segera Habis / Lewat</h3></div><table class="mini-table"><tbody>
@foreach($warnV as $v)@foreach([['STNK',$v->stnk_state],['Pajak',$v->tax_state]] as [$lbl,$st])@if($st && $st[0]!=='ok')<tr><td><a href="{{ route('admin.vehicles.show',$v) }}"><span class="plate">{{ $v->plate_number }}</span></a></td><td>{{ $lbl }} unit</td><td><span class="exp {{ $st[0] }}">{{ $st[1] }}</span></td></tr>@endif @endforeach @endforeach
@foreach($warnD as $d)@if($d->sim_state && $d->sim_state[0]!=='ok')<tr><td><a href="{{ route('admin.drivers.edit',$d) }}"><b>{{ $d->name }}</b></a></td><td>SIM supir</td><td><span class="exp {{ $d->sim_state[0] }}">{{ $d->sim_state[1] }}</span></td></tr>@endif @endforeach
</tbody></table></div>@endif
