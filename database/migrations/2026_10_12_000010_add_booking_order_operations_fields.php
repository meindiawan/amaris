<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('vehicles', function(Blueprint $t){ $t->string('fuel_barcode_file')->nullable()->after('stnk_file'); });
  Schema::table('drivers', function(Blueprint $t){ $t->string('sim_photo')->nullable()->after('photo'); });
  Schema::table('bookings', function(Blueprint $t){
   $t->string('invoice_status',30)->default('belum dibuat');
   $t->string('payment_state',30)->default('belum payment');
   $t->string('payment_received_by',120)->nullable();
   $t->text('payment_note')->nullable();
   $t->string('rental_name',150)->nullable();
   $t->string('booking_order_title',150)->nullable();
   $t->string('booking_service',150)->nullable();
   $t->string('booking_giat',150)->nullable();
   $t->string('standby_location',200)->nullable();
   $t->string('standby_time',30)->nullable();
   $t->string('guest_name',120)->nullable();
   $t->string('guest_phone',30)->nullable();
   $t->text('operation_notes')->nullable();
  });
 }
 public function down(): void {
  Schema::table('bookings', function(Blueprint $t){$t->dropColumn(['invoice_status','payment_state','payment_received_by','payment_note','rental_name','booking_order_title','booking_service','booking_giat','standby_location','standby_time','guest_name','guest_phone','operation_notes']);});
  Schema::table('drivers', fn(Blueprint $t)=>$t->dropColumn('sim_photo'));
  Schema::table('vehicles', fn(Blueprint $t)=>$t->dropColumn('fuel_barcode_file'));
 }
};
