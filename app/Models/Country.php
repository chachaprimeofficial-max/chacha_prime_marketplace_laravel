<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Country extends Model {
 protected $fillable=['code','name','flag','currency_code','active','sort_order'];
 protected $casts=['active'=>'boolean'];
 public function products(){return $this->hasMany(Product::class,'country_id');}
 public function marketplaceProducts(){return $this->belongsToMany(Product::class,'product_marketplaces');}
}
