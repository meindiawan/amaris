@extends('admin.layout')
@section('content')
<div class="page-head"><div><span>AKUN ADMIN</span><h2>Profil & Keamanan</h2><p>Ganti nama admin dan password login secara berkala.</p></div></div>
<form class="form-card" method="POST" action="{{ route('admin.profile.update') }}">@csrf
<div class="form-grid"><label>Nama Admin<input name="name" value="{{ old('name',auth()->user()->name) }}" required></label><label>Email Login<input value="{{ auth()->user()->email }}" disabled></label><label>Password Saat Ini<input type="password" name="current_password" required autocomplete="current-password"></label><label>Password Baru<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Minimal 8 karakter.</small></label><label>Konfirmasi Password<input type="password" name="password_confirmation" required autocomplete="new-password"></label></div><div class="form-actions"><button class="btn" type="submit">Simpan Perubahan</button></div></form>
@endsection
