@php
 $booking->loadMissing(['vehicle','driver','payments','invoice']);
 $rpx = fn($n) => 'Rp '.number_format($n,0,',','.');
 $showOrder = in_array($booking->status, ['confirmed','ongoing','completed']) && $booking->vehicle_id;
 $inv = $booking->invoice && $booking->invoice->is_published ? $booking->invoice : null;
@endphp
@if($showOrder)
<div style="text-align:left;margin:26px 0 6px;border-top:1px solid #eadfce;padding-top:22px">
 <h3 style="font-family:Manrope,sans-serif;margin:0 0 4px">Booking Order Anda</h3>
 <p class="hint" style="margin:0 0 10px">Detail unit &amp; supir yang bertugas. Simpan atau bagikan ke tamu / rombongan.</p>
 <div id="bo" style="white-space:pre-wrap;background:#f7f3ec;border:1px dashed #d9ccb9;border-radius:12px;padding:14px;font-size:14px;line-height:1.6">{{ $booking->orderText() }}</div>
 <div class="detail-cta" style="justify-content:flex-start;margin-top:12px"><button type="button" class="btn btn-outline" onclick="navigator.clipboard.writeText(document.getElementById('bo').innerText).then(()=>this.textContent='Tersalin ✓')">Salin teks</button><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode($booking->orderText()) }}">Bagikan via WhatsApp ↗</a></div>
</div>
@endif
@if($booking->paid_total > 0 || $inv)
<div style="text-align:left;margin:22px 0 6px;border-top:1px solid #eadfce;padding-top:22px">
 <h3 style="font-family:Manrope,sans-serif;margin:0 0 10px">Pembayaran &amp; Invoice</h3>
 <dl class="sum-list"><div><dt>Total tagihan</dt><dd>{{ $rpx($booking->bill_total) }}</dd></div><div><dt>Sudah dibayar</dt><dd>{{ $rpx($booking->paid_total) }}</dd></div><div><dt>Sisa</dt><dd><b>{{ $booking->balance_due > 0 ? $rpx($booking->balance_due) : 'LUNAS' }}</b></dd></div></dl>
 @if($inv)<div class="detail-cta" style="justify-content:flex-start"><a class="btn btn-primary" href="{{ $inv->publicUrl() }}">Lihat Invoice {{ $inv->number }}</a><a class="btn btn-outline" href="{{ $inv->publicPdfUrl(true) }}">Unduh PDF</a></div>@endif
</div>
@endif
