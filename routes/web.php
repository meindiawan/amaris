<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Car; use App\Models\Promo; use App\Models\Testimonial; use App\Models\Faq;
use App\Http\Controllers\Admin\AuthController; use App\Http\Controllers\Admin\DashboardController; use App\Http\Controllers\Admin\CarController; use App\Http\Controllers\Admin\PromoController; use App\Http\Controllers\Admin\TestimonialController; use App\Http\Controllers\Admin\SettingsController; use App\Http\Controllers\Admin\AdminController; use App\Http\Controllers\Admin\FaqController; use App\Http\Controllers\Admin\ProfileController; use App\Http\Controllers\Admin\BookingController as AdminBookingController; use App\Http\Controllers\BookingController;
Route::get('/', function(){
 $cars=Car::where('is_available',true)->orderBy('sort_order')->get(); $promos=Promo::where('is_active',true)->orderBy('sort_order')->get(); $testimonials=Testimonial::where('is_active',true)->orderBy('sort_order')->get(); $faqs=Faq::where('is_active',true)->orderBy('sort_order')->get(); $settings=DB::table('site_settings')->pluck('value','key'); return view('welcome',compact('cars','promos','testimonials','faqs','settings'));
});
Route::get('/armada/{car:slug}', function(\App\Models\Car $car){
 $settings=DB::table('site_settings')->pluck('value','key');
 $booked=\App\Models\Booking::where('car_id',$car->id)->whereIn('status',\App\Models\Booking::BLOCKING)->where('end_at','>=',now())->orderBy('start_at')->get(['start_at','end_at']);
 return view('car-detail', compact('car','settings','booked'));
})->name('car.show');
Route::post('/armada/{car:slug}/booking',[BookingController::class,'store'])->middleware('throttle:8,1')->name('booking.store');
Route::get('/booking/{code}',[BookingController::class,'success'])->name('booking.success');
Route::get('/admin/login',[AuthController::class,'show'])->name('admin.login');
Route::post('/admin/login',[AuthController::class,'login'])->middleware('throttle:6,1')->name('admin.login.post');
Route::middleware('auth')->prefix('admin')->group(function(){
 Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard'); Route::post('/logout',[AuthController::class,'logout'])->name('admin.logout');
 Route::resource('cars',CarController::class)->except(['show'])->names('admin.cars'); Route::resource('promos',PromoController::class)->except(['show'])->names('admin.promos'); Route::resource('testimonials',TestimonialController::class)->except(['show'])->names('admin.testimonials'); Route::resource('faqs',FaqController::class)->except(['show'])->names('admin.faqs');
 Route::get('/bookings',[AdminBookingController::class,'index'])->name('admin.bookings.index'); Route::get('/bookings/{booking}',[AdminBookingController::class,'show'])->name('admin.bookings.show'); Route::post('/bookings/{booking}/status',[AdminBookingController::class,'status'])->name('admin.bookings.status'); Route::delete('/bookings/{booking}',[AdminBookingController::class,'destroy'])->name('admin.bookings.destroy');
 Route::get('/settings',[SettingsController::class,'edit'])->name('admin.settings'); Route::get('/profile',[ProfileController::class,'edit'])->name('admin.profile'); Route::post('/profile',[ProfileController::class,'update'])->name('admin.profile.update'); Route::post('/settings',[SettingsController::class,'update']); Route::post('/logo',[AdminController::class,'uploadLogo'])->name('admin.logo');
});

// ===== Operasional rental: unit, supir, keluar-masuk, pembayaran, setoran, invoice =====
use App\Http\Controllers\Admin\VehicleController; use App\Http\Controllers\Admin\DriverController; use App\Http\Controllers\Admin\MovementController;
use App\Http\Controllers\Admin\PaymentController; use App\Http\Controllers\Admin\SetoranController; use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\InvoicePublicController;

Route::get('/cek-booking', [InvoicePublicController::class, 'lookupForm'])->name('booking.lookup');
Route::post('/cek-booking', [InvoicePublicController::class, 'lookup'])->middleware('throttle:10,1')->name('booking.lookup.post');
Route::get('/invoice/{token}', [InvoicePublicController::class, 'show'])->middleware('throttle:60,1')->name('invoice.public');
Route::get('/invoice/{token}/pdf', [InvoicePublicController::class, 'pdf'])->middleware('throttle:30,1')->name('invoice.public.pdf');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('vehicles-labels', [VehicleController::class, 'labels'])->name('admin.vehicles.labels');
    Route::get('vehicles/{vehicle}/label', [VehicleController::class, 'label'])->name('admin.vehicles.label');
    Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
    Route::resource('drivers', DriverController::class)->except(['show'])->names('admin.drivers');

    Route::get('movements', [MovementController::class, 'index'])->name('admin.movements.index');
    Route::get('movements/scan', [MovementController::class, 'scan'])->name('admin.movements.scan');
    Route::get('movements/create', [MovementController::class, 'create'])->name('admin.movements.create');
    Route::post('movements', [MovementController::class, 'store'])->name('admin.movements.store');
    Route::delete('movements/{movement}', [MovementController::class, 'destroy'])->name('admin.movements.destroy');

    Route::get('payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('admin.payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('admin.payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    Route::resource('setoran', SetoranController::class)->except(['show'])->parameters(['setoran' => 'setoran'])->names('admin.setoran');

    Route::get('invoices', [AdminInvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::post('bookings/{booking}/invoice', [AdminInvoiceController::class, 'fromBooking'])->name('admin.bookings.invoice');
    Route::post('bookings/{booking}/assign', [AdminBookingController::class, 'assign'])->name('admin.bookings.assign');
    Route::get('invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::get('invoices/{invoice}/edit', [AdminInvoiceController::class, 'edit'])->name('admin.invoices.edit');
    Route::put('invoices/{invoice}', [AdminInvoiceController::class, 'update'])->name('admin.invoices.update');
    Route::delete('invoices/{invoice}', [AdminInvoiceController::class, 'destroy'])->name('admin.invoices.destroy');
    Route::get('invoices/{invoice}/pdf', [AdminInvoiceController::class, 'pdf'])->name('admin.invoices.pdf');
    Route::post('invoices/{invoice}/publish', [AdminInvoiceController::class, 'publish'])->name('admin.invoices.publish');
    Route::post('invoices/{invoice}/rebuild', [AdminInvoiceController::class, 'rebuild'])->name('admin.invoices.rebuild');
});
