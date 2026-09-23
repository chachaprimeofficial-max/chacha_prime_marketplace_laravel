<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
 protected $fillable=['vendor_id','category_id','brand_id','name','slug','sku','description','short_description','highlights','specifications','shipping_info','features','additional_details','return_policy','video_urls','retail_price','wholesale_price','factory_price','cost_price','currency','stock','stock_status','status','featured','weight'];
 protected $casts=['retail_price'=>'decimal:2','cost_price'=>'decimal:2','stock'=>'decimal:3','featured'=>'boolean'];
 public function vendor(){return $this->belongsTo(Vendor::class);}
 public function category(){return $this->belongsTo(Category::class);}
 public function images(){return $this->hasMany(ProductImage::class);}
 public function variants(){return $this->hasMany(ProductVariant::class);}
 public function pricingTiers(){return $this->hasMany(PricingTier::class);} public function videos(){return $this->hasMany(ProductVideo::class);}
}