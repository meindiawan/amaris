@extends('admin.layout') @section('content')
<div class="page-head"><div><span>KEUANGAN</span><h2>Invoice</h2><p>Invoice dibuat otomatis dari halaman detail booking.</p></div>
<form class="search-box" method="GET"><input name="q" value="{{ $q }}" placeholder="Cari nomor / nama pelanggan…"><button class="btn">Cari</button></form></div>
<div class="table-card"><table><thead><tr><th>No. Invoice</th><th>Kepada</th><th>Tanggal</th><th>Total</th><th>Dibayar</th><th>Sisa</th><th>Pelanggan</th><th></th></tr></thead><tbody>
@forelse($invoices as $i)<tr onclick="location='{{ route('admin.invoices.show',$i) }}'" style="cursor:pointer">
<td><b>{{ $i->number }}</b><small>{{ $i->booking->code ?? 'tanpa booking' }}</small></td><td>{{ $i->bill_to_name }}</td><td>{{ $i->issued_at->format('d M Y') }}</td>
<td>Rp {{ number_format($i->net,0,',','.') }}</td><td>Rp {{ number_format($i->paid,0,',','.') }}</td>
<td><span class="status pay-{{ $i->status }}">{{ $i->status==='paid'?'Lunas':'Rp '.number_format($i->due,0,',','.') }}</span></td>
<td>{{ $i->is_published?'Terlihat':'Disembunyikan' }}</td><td class="actions"><a href="{{ route('admin.invoices.show',$i) }}">Buka →</a></td></tr>
@empty<tr><td colspan="8" class="empty">Belum ada invoice. Buka detail booking lalu klik "Buat Invoice".</td></tr>@endforelse</tbody></table></div>
@if($invoices->hasPages())<div class="pager"><span>Halaman {{ $invoices->currentPage() }} / {{ $invoices->lastPage() }}</span>@if($invoices->previousPageUrl())<a class="btn secondary" href="{{ $invoices->previousPageUrl() }}">← Sebelumnya</a>@endif @if($invoices->nextPageUrl())<a class="btn secondary" href="{{ $invoices->nextPageUrl() }}">Berikutnya →</a>@endif</div>@endif
@endsection
