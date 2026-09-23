<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariant extends Model {
 public $timestamps=false;
 protected $fillable=['product_id','sku','name','attributes','price','stock','status'];
 protected $casts=['attributes'=>'array','price'=>'decimal:2','stock'=>'decimal:3','status'=>'boolean'];
 public function product(){return $this->belongsTo(Product::class);}
}