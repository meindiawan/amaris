@php
 use App\Support\Fmt;
 $logo = Fmt::dataUri('images/invoice/logo.png');
 $ttd  = Fmt::dataUri('images/invoice/ttd.png');
 $bankName = Fmt::setting('bank_name', 'BCA');
 $bankAcc  = Fmt::setting('bank_account', '4620 707070');
 $bankHold = Fmt::setting('bank_holder', 'Damaris Eka Wijayanti');
 $signer   = Fmt::setting('invoice_signer', 'Ardiansyah Kemas');
 $note     = trim((string) ($invoice->notes ?: Fmt::setting('invoice_note', '')));
 $rp = function ($n) { return '<table class="rpt"><tr><td>Rp</td><td class="n">'.Fmt::num($n).'</td></tr></table>'; };
@endphp
@unless($noCss ?? false)@include('invoices._css')@endunless
<div class="inv">
 <table class="inv-head"><tr>
  <td class="inv-logo">@if($logo)<img src="{{ $logo }}" alt="Amaris Rent Car">@else<b style="font-size:18pt">AMARIS RENT CAR</b>@endif</td>
  <td class="inv-title"><div class="t">INVOICE</div><div>No &nbsp;&nbsp;&nbsp;&nbsp;: {{ $invoice->number }}</div><div>Date &nbsp;: {{ Fmt::date($invoice->issued_at) }}</div></td>
 </tr></table>

 <div class="inv-to">Kepada Yth :<br>{{ $invoice->bill_to_name }}@if($invoice->bill_to_city)<br>{{ $invoice->bill_to_city }}@endif @if($invoice->bill_to_note)<br>{{ $invoice->bill_to_note }}@endif</div>

 <table class="inv-items">
  <thead><tr><th style="width:5%">No</th><th style="width:19%">MOBIL</th><th style="width:31%">TGL &amp; RUTE</th><th style="width:6%">UNIT</th><th style="width:6%">HARI</th><th style="width:16%">HARGA / HARI</th><th style="width:17%">JUMLAH</th></tr></thead>
  <tbody>
  @php $no = 0; @endphp
  @foreach($invoice->items as $it)
   @php if ($it->kind === 'main') $no++; @endphp
   <tr class="{{ $it->kind === 'main' ? 'first' : '' }}">
    <td class="c">{{ $it->kind === 'main' ? $no : '' }}</td>
    <td>@foreach($it->mobilLines() as $l){{ $l }}<br>@endforeach</td>
    <td>@foreach($it->ruteLines() as $l){{ $it->kind === 'extra' ? 'Ø ' : '' }}{{ $l }}<br>@endforeach</td>
    <td class="c">{{ $it->qty }}</td>
    <td class="c">{{ $it->days }}</td>
    <td class="rp">{!! $it->price !== null ? $rp($it->price) : '' !!}</td>
    <td class="rp">{!! $rp($it->amount) !!}</td>
   </tr>
  @endforeach
  <tr class="pad"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
  </tbody>
 </table>

 <table class="inv-foot"><tr>
  <td style="width:52%">
   <div class="inv-bank">Cara Pembayaran Transfer :</div>
   <table class="inv-bank"><tr><td style="width:34px">Bank</td><td>: {{ $bankName }}</td></tr><tr><td>Ac</td><td>: {{ $bankAcc }}</td></tr><tr><td>An</td><td>: {{ $bankHold }}</td></tr></table>
  </td>
  <td style="width:48%">
   <table class="inv-tot">
    <tr><td style="width:40%">SUB TOTAL</td><td>{!! $rp($invoice->subtotal) !!}</td></tr>
    @if($invoice->discount > 0)<tr><td>{{ $invoice->discount_label ?: 'DISKON' }}</td><td>{!! $rp($invoice->discount) !!}</td></tr>@endif
    @if($invoice->paid > 0)<tr><td>{{ $invoice->paid_label }}</td><td>{!! $rp($invoice->paid) !!}</td></tr>@endif
    <tr class="grand"><td>TOTAL</td><td>{!! $rp($invoice->due) !!}</td></tr>
   </table>
   <div class="inv-state"><span class="{{ $invoice->status }}">{{ strtoupper($invoice->status_label) }}</span></div>
  </td>
 </tr></table>

 <div class="inv-sign"><div>Hormat Kami,</div>@if($ttd)<div class="ttd"><img src="{{ $ttd }}" alt=""></div>@else<div style="height:60px"></div>@endif<div>{{ $signer }}</div></div>
 @if($note)<div class="inv-note">{!! nl2br(e($note)) !!}</div>@endif
</div>
