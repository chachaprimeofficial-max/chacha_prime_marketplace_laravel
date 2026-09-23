<?php
namespace AppModels;
use IlluminateDatabaseEloquentModel;
class OrderItem extends Model {
 public function order(){return $this->belongsTo(Order::class);}
 public function product(){return $this->belongsTo(Product::class);}
 public function vendor(){return $this->belongsTo(Vendor::class);}
 protected $fillable=['order_id','vendor_id','product_id','variant_id','product_name','sku','quantity','unit_price','subtotal','vendor_status'];
}