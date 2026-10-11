@php
 $vOut = \App\Models\Vehicle::where('is_out',true)->count(); $vAll = \App\Models\Vehicle::where('is_active',true)->count();
 $unpaid = \App\Models\VehicleLog::where('payment_status','belum')->count();
 $noInv = \App\Models\VehicleLog::where('invoice_status','!=','sudah_terkirim')->count();
 $needAssign = \App\Models\Booking::whereIn('status',['pending','confirmed'])->where('end_at','>=',now())->get()->filter(fn($b)=>!$b->is_assigned)->count();
@endphp
<div class="stats"><div><small>Unit Keluar</small><strong>{{ $vOut }}<span style="display:inline;font-size:16px"> / {{ $vAll }}</span></strong><span><a href="{{ route('admin.movements.index') }}">papan garasi →</a></span></div><div><small>Belum Ditugaskan</small><strong>{{ $needAssign }}</strong><span>booking butuh unit / supir</span></div><div><small>Payment Belum Masuk</small><strong>{{ $unpaid }}</strong><span><a href="{{ route('admin.movements.index',['f'=>'unpaid']) }}">lihat catatan →</a></span></div><div><small>Invoice Belum Terkirim</small><strong>{{ $noInv }}</strong><span><a href="{{ route('admin.movements.index',['f'=>'noinvoice']) }}">lihat catatan →</a></span></div></div>
