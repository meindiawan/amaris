<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Car; use Illuminate\Http\Request; use Illuminate\Support\Str;
class CarController extends Controller {
 public function index(){return view('admin.cars.index',['cars'=>Car::orderBy('sort_order')->latest()->get()]);}
 public function create(){return view('admin.cars.form',['car'=>new Car]);}
 public function store(Request $r){$data=$this->validateData($r);$data['is_available']=$r->boolean('is_available');$data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5));$data['image']=$this->image($r);Car::create($data);return redirect('/admin/cars')->with('ok','Mobil berhasil ditambahkan.');}
 public function edit(Car $car){return view('admin.cars.form',compact('car'));}
 public function update(Request $r,Car $car){$data=$this->validateData($r);$data['is_available']=$r->boolean('is_available');$data['slug']=$car->slug;$img=$this->image($r);if($img)$data['image']=$img;$car->update($data);return redirect('/admin/cars')->with('ok','Data mobil diperbarui.');}
 public function destroy(Car $car){$car->delete();return back()->with('ok','Mobil dihapus.');}
 private function validateData($r){return $r->validate(['name'=>'required','type'=>'nullable','transmission'=>'nullable','seats'=>'required|integer|min:1|max:20','price'=>'required|integer|min:0','image'=>'nullable','badge'=>'nullable|max:40','description'=>'nullable','is_available'=>'nullable','sort_order'=>'nullable|integer']);}
 private function image($r){if(!$r->hasFile('image'))return null;$file=$r->file('image');$name=Str::uuid().'.'.$file->getClientOriginalExtension();$file->move(public_path('uploads/cars'),$name);return '/uploads/cars/'.$name;}
}
