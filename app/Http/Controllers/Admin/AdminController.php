<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class AdminController extends Controller { public function uploadLogo(Request $r){$r->validate(['logo'=>'required|image|max:2048']);$f=$r->file('logo');$f->move(public_path('images'),'logo-amaris.'.$f->getClientOriginalExtension());return back()->with('ok','Logo diperbarui.');} }
