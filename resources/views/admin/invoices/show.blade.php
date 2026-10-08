@extends('admin.layout') @section('content')
@php
 $wa = $invoice->booking ? 'https://wa.me/'.$invoice->booking->wa_phone.'?text='.urlencode("Halo {$invoice->bill_to_name}, berikut invoice {$invoice->number} dari Amaris Rent Car (total ".\App\Support\Fmt::rp($invoice->net)."):\n".$invoice->publicUrl()) : null;
@endphp
<div class="page-head"><div><span>INVOICE</span><h2>{{ $invoice->number }}</h2><p><span class="status pay-{{ $invoice->status }}">{{ $invoice->status_label }}</span> · {{ $invoice->is_published?'Terlihat oleh pelanggan':'Belum dikirim ke pelanggan' }}</p></div>
<div class="head-actions"><a class="btn" href="{{ route('admin.invoices.pdf',$invoice) }}" target="_blank">Lihat PDF</a><a class="btn secondary" href="{{ route('admin.invoices.pdf',[$invoice,'download'=>1]) }}">Unduh PDF</a>@if($wa)<a class="btn wa" href="{{ $wa }}" target="_blank" rel="noopener">Kirim WhatsApp ↗</a>@endif<a class="btn secondary" href="{{ route('admin.invoices.edit',$invoice) }}">Edit</a></div></div>
<div class="head-actions" style="margin-bottom:16px">
<form method="POST" action="{{ route('admin.invoices.publish',$invoice) }}">@csrf<button class="btn {{ $invoice->is_published?'secondary':'' }}">{{ $invoice->is_published?'Sembunyikan dari pelanggan':'Kirim ke pelanggan (tampilkan)' }}</button></form>
@if($invoice->booking)<form method="POST" action="{{ route('admin.invoices.rebuild',$invoice) }}" onsubmit="return confirm('Susun ulang baris dari booking? Perubahan manual pada baris akan hilang.')">@csrf<button class="btn secondary">↻ Susun ulang dari booking</button></form><a class="btn secondary" href="{{ route('admin.bookings.show',$invoice->booking) }}">Ke booking {{ $invoice->booking->code }}</a>@endif</div>
@if($invoice->is_published)<p class="no-print" style="color:var(--muted)">Tautan pelanggan: <a href="{{ $invoice->publicUrl() }}" target="_blank">{{ $invoice->publicUrl() }}</a></p>@endif
<div class="paper-wrap"><div class="paper">@include('invoices._sheet')</div></div>
<form method="POST" action="{{ route('admin.invoices.destroy',$invoice) }}" onsubmit="return confirm('Hapus invoice ini?')" style="margin-top:20px">@csrf @method('DELETE')<button class="link-danger" style="width:auto">Hapus invoice</button></form>
@endsection
