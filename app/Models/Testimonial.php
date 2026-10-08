<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Testimonial extends Model { protected $fillable=['name','role','text','rating','initial','is_active','sort_order']; protected $casts=['is_active'=>'boolean']; }
