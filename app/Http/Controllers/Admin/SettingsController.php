<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class SettingsController extends Controller { public function edit(){ $settings=DB::table('site_settings')->pluck('value','key'); return view('admin.settings',compact('settings')); } public function update(Request $r){foreach(['whatsapp','email','instagram','tiktok','address','about','terms','driver_fee','allin_fee','delivery_fee','bank_name','bank_account','bank_holder','invoice_signer','invoice_prefix','invoice_note','site_url','documentary_url'] as $k)DB::table('site_settings')->updateOrInsert(['key'=>$k],['value'=>$r->input($k),'updated_at'=>now(),'created_at'=>now()]);return back()->with('ok','Pengaturan disimpan.');} }
