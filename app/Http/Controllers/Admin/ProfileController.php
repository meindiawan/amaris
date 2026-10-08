<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller {
 public function edit(){return view('admin.profile');}
 public function update(Request $r){$d=$r->validate(['name'=>'required|string|max:120','current_password'=>'required','password'=>'required|string|min:8|confirmed']); if(!Hash::check($d['current_password'],$r->user()->password)){return back()->withErrors(['current_password'=>'Password saat ini salah.']);} $r->user()->update(['name'=>$d['name'],'password'=>$d['password']]); return back()->with('ok','Profil dan password berhasil diperbarui.');}
}
