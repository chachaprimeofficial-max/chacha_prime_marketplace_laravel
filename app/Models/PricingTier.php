<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PricingTier extends Model {
 protected $fillable=['product_id','customer_type','min_quantity','max_quantity','price','discount_percent'];
 protected $casts=['min_quantity'=>'decimal:3','max_quantity'=>'decimal:3','price'=>'decimal:2','discount_percent'=>'decimal:3'];
 public function product(){return $this->belongsTo(Product::class);}
}