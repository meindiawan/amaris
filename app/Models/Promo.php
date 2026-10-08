<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Promo extends Model { protected $fillable=['eyebrow','title','text','image','button_text','button_url','is_active','sort_order']; protected $casts=['is_active'=>'boolean']; }
