<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Car extends Model { protected $fillable=['name','slug','type','transmission','seats','price','image','badge','description','is_available','sort_order']; protected $casts=['is_available'=>'boolean']; }
