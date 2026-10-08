@extends('admin.layout') @section('content')
<div class="page-head"><div><span>INVOICE</span><h2>Edit {{ $invoice->number }}</h2><p>Tambah baris untuk BBM, tol, parkir, overtime, uang makan, inap supir, dll.</p></div></div>
<form method="POST" action="{{ route('admin.invoices.update',$invoice) }}">@csrf @method('PUT')
<div class="form-card"><div class="form-grid">
<label>Nomor invoice<input name="number" value="{{ old('number',$invoice->number) }}" required></label>
<label>Tanggal<input type="date" name="issued_at" value="{{ old('issued_at',$invoice->issued_at->format('Y-m-d')) }}" required></label>
<label>Kepada Yth (nama)<input name="bill_to_name" value="{{ old('bill_to_name',$invoice->bill_to_name) }}" required></label>
<label>Kota<input name="bill_to_city" value="{{ old('bill_to_city',$invoice->bill_to_city) }}" placeholder="Semarang"></label>
<label class="full">Keterangan di bawah nama<input name="bill_to_note" value="{{ old('bill_to_note',$invoice->bill_to_note) }}" placeholder="(Pelayanan BJP Rudi)"></label>
<label>Label potongan<input name="discount_label" value="{{ old('discount_label',$invoice->discount_label) }}" placeholder="DISKON"></label>
<label>Potongan / diskon (Rp)<input type="number" min="0" name="discount" value="{{ old('discount',$invoice->discount) }}"></label>
@unless($invoice->booking_id)<label>Sudah dibayar (Rp)<input type="number" min="0" name="paid_manual" value="{{ old('paid_manual',$invoice->paid_manual) }}"><small>Untuk invoice tanpa booking. Invoice dari booking otomatis memakai catatan Pembayaran.</small></label>@endunless
<label class="full">Catatan (muncul di kaki invoice)<textarea name="notes">{{ old('notes',$invoice->notes) }}</textarea></label></div></div>
<div class="form-card" style="max-width:none;margin-top:20px"><h3>Baris Invoice</h3>
<table class="edit-items" id="items"><thead><tr><th style="width:90px">Jenis</th><th>MOBIL (1 baris = 1 baris cetak)</th><th>TGL &amp; RUTE / keterangan</th><th style="width:60px">Unit</th><th style="width:60px">Hari</th><th style="width:110px">Harga/hari</th><th style="width:120px">Jumlah</th><th style="width:30px"></th></tr></thead><tbody>
@foreach($invoice->items as $n=>$it)@include('admin.invoices._row',['n'=>$n,'it'=>$it])@endforeach</tbody></table>
<p><button type="button" class="btn secondary" onclick="addRow('main')">+ Baris unit mobil</button> <button type="button" class="btn secondary" onclick="addRow('extra')">+ Biaya tambahan (Ø)</button></p>
<small>Jumlah otomatis = harga × hari bila kolom Jumlah dikosongkan. Baris "tambahan" tampil dengan tanda Ø.</small></div>
<div class="form-actions" style="max-width:1000px"><a class="btn secondary" href="{{ route('admin.invoices.show',$invoice) }}">Batal</a><button class="btn">Simpan Invoice →</button></div></form>
<template id="tpl">@include('admin.invoices._row',['n'=>'__N__','it'=>new \App\Models\InvoiceItem(['kind'=>'main'])])</template>
<script>
var idx={{ $invoice->items->count() }};
function addRow(kind){var h=document.getElementById('tpl').innerHTML.split('__N__').join(idx++);var t=document.createElement('tbody');t.innerHTML=h;var r=t.firstElementChild;r.querySelector('select').value=kind;document.querySelector('#items tbody').appendChild(r);}
document.addEventListener('input',function(e){var r=e.target.closest('tr');if(!r)return;if(e.target.name&&(e.target.name.endsWith('[price]')||e.target.name.endsWith('[days]'))){var p=+r.querySelector('[name$="[price]"]').value,d=+r.querySelector('[name$="[days]"]').value;if(p&&d)r.querySelector('[name$="[amount]"]').value=p*d;}});
</script>
@endsection
