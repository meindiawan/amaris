<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Car; use App\Models\Promo; use App\Models\Testimonial; use App\Models\Faq; use App\Models\Booking;
class DashboardController extends Controller { public function index(){return view('admin.dashboard',['cars'=>Car::count(),'available'=>Car::where('is_available',true)->count(),'promos'=>Promo::where('is_active',true)->count(),'testimonials'=>Testimonial::where('is_active',true)->count(),'faqs'=>Faq::where('is_active',true)->count(),'newBookings'=>Booking::where('status','pending')->count(),'activeBookings'=>Booking::whereIn('status',Booking::BLOCKING)->count(),'recent'=>Booking::latest()->take(6)->get()]);} }
