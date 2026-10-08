<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('cars', function(Blueprint $table){$table->id();$table->string('name');$table->string('slug')->unique();$table->string('type')->nullable();$table->string('transmission')->nullable();$table->unsignedTinyInteger('seats')->default(5);$table->unsignedInteger('price');$table->string('image')->nullable();$table->string('badge')->nullable();$table->text('description')->nullable();$table->boolean('is_available')->default(true);$table->unsignedInteger('sort_order')->default(0);$table->timestamps();});
  Schema::create('promos', function(Blueprint $table){$table->id();$table->string('eyebrow')->nullable();$table->string('title');$table->text('text')->nullable();$table->string('image');$table->string('button_text')->default('Lihat Armada');$table->string('button_url')->default('#armada');$table->boolean('is_active')->default(true);$table->unsignedInteger('sort_order')->default(0);$table->timestamps();});
  Schema::create('testimonials', function(Blueprint $table){$table->id();$table->string('name');$table->string('role')->default('Customer');$table->text('text');$table->unsignedTinyInteger('rating')->default(5);$table->string('initial',2)->nullable();$table->boolean('is_active')->default(true);$table->unsignedInteger('sort_order')->default(0);$table->timestamps();});
  Schema::create('site_settings', function(Blueprint $table){$table->id();$table->string('key')->unique();$table->text('value')->nullable();$table->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('site_settings');Schema::dropIfExists('testimonials');Schema::dropIfExists('promos');Schema::dropIfExists('cars');}
};
