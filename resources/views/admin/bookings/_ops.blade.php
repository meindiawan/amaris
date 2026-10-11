@php $rp = fn($n)=>'Rp '.number_format($n,0,',','.'); $vehicles = \App\Models\Vehicle::where('is_active',true)->orderBy('plate_number')->get(); $drivers = \App\Models\Driver::where('is_active',true)->orderBy('name')->get(); $booking->loadMissing(['vehicle','driver','payments','invoice']); @endphp
<div class="form-card"><h3>Penugasan Unit & Supir · Booking Order</h3>
<form method="POST" action="{{ route('admin.bookings.assign',$booking) }}">@csrf
<div class="inline-form">
<label>Unit (plat nomor)<select name="vehicle_id"><option value="">— pilih unit —</option>@foreach($vehicles->sortByDesc(fn($v)=>$v->car_id==$booking->car_id) as $v)<option value="{{ $v->id }}" @selected(old('vehicle_id',$booking->vehicle_id)==$v->id)>{{ $v->plate_number }} · {{ $v->label }}{{ $v->car_id==$booking->car_id?' ★':'' }}</option>@endforeach</select></label>
<label>Supir<select name="driver_id"><option value="">— tanpa supir —</option>@foreach($drivers as $d)<option value="{{ $d->id }}" @selected(old('driver_id',$booking->driver_id)==$d->id)>{{ $d->name }} · {{ $d->phone }}</option>@endforeach</select></label>
<label class="full">Booking order / nama kegiatan<input name="order_name" value="{{ old('order_name',$booking->order_name ?: $booking->customer_name) }}" placeholder="BP Dwi Polda"></label>
<label>Nama rental<input name="rental_name" value="{{ old('rental_name',$booking->rental_name ?: 'Amaris Rentcar') }}"></label>
<label>Pelayanan (laporan)<input name="booking_service" value="{{ old('booking_service',$booking->booking_service ?: $booking->pelayanan) }}"></label>
<label>Pelayanan<input name="pelayanan" value="{{ old('pelayanan',$booking->pelayanan) }}"></label>
<label>Giat<input name="giat" value="{{ old('giat',$booking->giat) }}" placeholder="TB1"></label>
<label>Standby di<input name="standby_location" value="{{ old('standby_location',$booking->standby_location) }}" placeholder="Bandara Semarang"></label>
<label>Jam standby<input name="standby_time" value="{{ old('standby_time',$booking->standby_time ?: $booking->start_at->format('H.i').' WIB') }}"></label>
<label class="full">Tujuan<input name="destination" value="{{ old('destination',$booking->destination) }}"></label>
<label class="full">Keterangan<textarea name="operation_notes" rows="2">{{ old('operation_notes',$booking->operation_notes) }}</textarea></label>
<label>Kontak tamu — nama<input name="guest_name" value="{{ old('guest_name',$booking->guest_name) }}"></label>
<label>Kontak tamu — telp<input name="guest_phone" value="{{ old('guest_phone',$booking->guest_phone) }}"></label>
</div>
<div class="inline-form" style="margin-top:14px">
<label>Status invoice<select name="invoice_status"><option @selected(old('invoice_status',$booking->invoice_status)==='belum dibuat')>belum dibuat</option><option @selected(old('invoice_status',$booking->invoice_status)==='sudah dibuat')>sudah dibuat</option><option @selected(old('invoice_status',$booking->invoice_status)==='sudah terkirim')>sudah terkirim</option></select></label>
<label>Status payment<select name="payment_state"><option @selected(old('payment_state',$booking->payment_state)==='belum payment')>belum payment</option><option @selected(old('payment_state',$booking->payment_state)==='sudah payment')>sudah payment</option></select></label>
<label>Payment diterima oleh<input name="payment_received_by" value="{{ old('payment_received_by',$booking->payment_received_by) }}" placeholder="Nama penerima pembayaran"></label>
<label class="full">Catatan payment<textarea name="payment_note" rows="2" placeholder="Keterangan pembayaran">{{ old('payment_note',$booking->payment_note) }}</textarea></label>
</div><button class="btn" style="margin-top:14px">Simpan Penugasan & Booking Order →</button><small>Unit ★ = jenis mobil yang dipesan. Bentrok jadwal unit/supir dicek otomatis untuk booking yang dikonfirmasi.</small></form>
@if($booking->vehicle)
<h3 style="margin-top:24px">Booking Order (kirim ke pelanggan)</h3><div class="order-text" id="ordertxt">{{ $booking->orderText() }}</div>
<div class="head-actions"><button type="button" class="btn secondary" onclick="navigator.clipboard.writeText(document.getElementById('ordertxt').innerText).then(()=>this.textContent='Tersalin ✓')">Salin teks</button><a class="btn wa" target="_blank" rel="noopener" href="https://wa.me/{{ $booking->wa_phone }}?text={{ urlencode($booking->orderText()) }}">Kirim ke pelanggan via WhatsApp ↗</a></div>
<small>Pelanggan juga melihat Booking Order ini di halaman booking mereka setelah status Dikonfirmasi.</small>@endif
</div>
