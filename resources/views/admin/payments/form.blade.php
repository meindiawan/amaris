@extends('admin.layout') @section('content')
<div class="page-head"><div><span>KEUANGAN</span><h2>Catat Pembayaran</h2></div></div>
<form class="form-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.payments.store') }}">@csrf
<div class="form-grid">
<label class="full">Booking<select name="booking_id" required>@foreach($bookings as $b)<option value="{{ $b->id }}" @selected(old('booking_id',$bookingId)==$b->id)>{{ $b->code }} · {{ $b->customer_name }} · {{ $b->car_name }} · {{ $b->start_at->format('d M Y') }}</option>@endforeach</select></label>
<label>Jenis<select name="type">@foreach(\App\Models\Payment::TYPES as $k=>$l)<option value="{{ $k }}" @selected(old('type')===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Jumlah (Rp)<input type="number" name="amount" min="1" value="{{ old('amount') }}" required></label>
<label>Metode<select name="method">@foreach(\App\Models\Payment::METHODS as $k=>$l)<option value="{{ $k }}" @selected(old('method')===$k)>{{ $l }}</option>@endforeach</select></label>
<label>Tanggal bayar<input type="datetime-local" name="paid_at" value="{{ old('paid_at',now()->format('Y-m-d\TH:i')) }}" required></label>
<label>Referensi / catatan<input name="reference" value="{{ old('reference') }}" placeholder="No. transfer, atas nama pengirim…"></label>
<label>Bukti transfer (foto)<input type="file" name="proof" accept="image/*"></label>
</div><div class="form-actions"><a class="btn secondary" href="{{ route('admin.payments.index') }}">Batal</a><button class="btn">Simpan →</button></div></form>
@endsection
