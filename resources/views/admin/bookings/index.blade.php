@extends('admin.layout') @section('content')
<div class="page-head"><div><span>RENTAL ORDERS</span><h2>Booking Masuk</h2><p>Semua pesanan dari formulir booking di website tersimpan di sini.</p></div>
<form class="search-box" method="GET" action="{{ route('admin.bookings.index') }}">@if($status)<input type="hidden" name="status" value="{{ $status }}">@endif<input name="q" value="{{ $q }}" placeholder="Cari nama, kode, no. HP, mobil…"><button class="btn">Cari</button></form></div>
<div class="filter-tabs">
 <a href="{{ route('admin.bookings.index',['q'=>$q?:null]) }}" class="{{ !$status?'on':'' }}">Semua <b>{{ $counts->sum() }}</b></a>
 @foreach(\App\Models\Booking::STATUSES as $k=>$label)<a href="{{ route('admin.bookings.index',['status'=>$k,'q'=>$q?:null]) }}" class="{{ $status===$k?'on':'' }}">{{ $label }} <b>{{ $counts[$k] ?? 0 }}</b></a>@endforeach
</div>
<div class="table-card"><table><thead><tr><th>Kode</th><th>Pelanggan</th><th>Mobil & Paket</th><th>Jadwal</th><th>Estimasi</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($bookings as $b)<tr>
 <td><b>{{ $b->code }}</b><small>{{ $b->created_at->format('d M Y H:i') }}</small></td>
 <td><b>{{ $b->customer_name }}</b><small>{{ $b->phone }}</small></td>
 <td>{{ $b->car_name }}<small>{{ $b->service_label }}</small></td>
 <td>{{ $b->start_at->format('d M Y, H:i') }}<small>s/d {{ $b->end_at->format('d M Y, H:i') }} · {{ $b->days }} hari</small></td>
 <td><b>Rp {{ number_format($b->total_estimate,0,',','.') }}</b></td>
 <td><span class="status st-{{ $b->status }}">{{ $b->status_label }}</span></td>
 <td class="actions"><a href="{{ route('admin.bookings.show',$b) }}">Detail →</a></td></tr>
@empty<tr><td colspan="7" class="empty">Belum ada booking{{ ($status||$q)?' yang cocok dengan filter':'' }}.</td></tr>@endforelse
</tbody></table></div>
@if($bookings->hasPages())<div class="pager"><span>Halaman {{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}</span>@if($bookings->previousPageUrl())<a class="btn secondary" href="{{ $bookings->previousPageUrl() }}">← Sebelumnya</a>@endif @if($bookings->nextPageUrl())<a class="btn secondary" href="{{ $bookings->nextPageUrl() }}">Berikutnya →</a>@endif</div>@endif
@endsection
