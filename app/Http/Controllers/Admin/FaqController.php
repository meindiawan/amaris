<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
class FaqController extends Controller {
 public function index(){return view('admin.faqs.index',['faqs'=>Faq::orderBy('sort_order')->get()]);}
 public function create(){return view('admin.faqs.form',['faq'=>new Faq(['is_active'=>true])]);}
 public function store(Request $r){$d=$this->data($r);$d['is_active']=$r->boolean('is_active');Faq::create($d);return redirect()->route('admin.faqs.index')->with('ok','FAQ berhasil ditambahkan.');}
 public function edit(Faq $faq){return view('admin.faqs.form',compact('faq'));}
 public function update(Request $r,Faq $faq){$d=$this->data($r);$d['is_active']=$r->boolean('is_active');$faq->update($d);return redirect()->route('admin.faqs.index')->with('ok','FAQ diperbarui.');}
 public function destroy(Faq $faq){$faq->delete();return back()->with('ok','FAQ dihapus.');}
 private function data(Request $r){return $r->validate(['question'=>'required|string|max:255','answer'=>'required|string','sort_order'=>'nullable|integer','is_active'=>'nullable']);}
}
