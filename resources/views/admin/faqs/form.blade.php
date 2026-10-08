@extends('admin.layout')
@section('content')
<div class="page-head"><div><span>KONTEN WEBSITE</span><h2>{{ $faq->exists?'Edit FAQ':'Tambah FAQ' }}</h2><p>Tulis jawaban singkat dan jelas agar mudah dibaca di HP.</p></div></div>
<form class="form-card" method="POST" action="{{ $faq->exists?route('admin.faqs.update',$faq):route('admin.faqs.store') }}">@csrf @if($faq->exists) @method('PUT') @endif
<div class="form-grid"><label class="full">Pertanyaan<input name="question" value="{{ old('question',$faq->question) }}" required placeholder="Apakah bisa sewa mobil untuk perjalanan luar kota?"></label><label class="full">Jawaban<textarea name="answer" required>{{ old('answer',$faq->answer) }}</textarea></label><label>Urutan<input type="number" name="sort_order" value="{{ old('sort_order',$faq->sort_order ?? 0) }}" min="0"><small>Angka kecil tampil lebih dulu.</small></label></div><label class="switch"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$faq->exists?$faq->is_active:true)?'checked':'' }}> Tampilkan di website</label><div class="form-actions"><a class="btn secondary" href="{{ route('admin.faqs.index') }}">Batal</a><button class="btn" type="submit">Simpan FAQ</button></div>
</form>
@endsection
