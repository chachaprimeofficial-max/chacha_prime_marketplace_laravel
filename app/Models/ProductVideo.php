<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVideo extends Model {
 protected $fillable=['product_id','path','title','sort_order'];
 public function product(){return $this->belongsTo(Product::class);}
}