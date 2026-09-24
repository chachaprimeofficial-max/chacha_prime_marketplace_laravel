<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShippingMethod extends Model {protected $fillable=['shipping_zone_id','name','code','rate_type','base_rate','per_kg_rate','free_over','min_weight','max_weight','min_days','max_days','currency','active'];protected $casts=['base_rate'=>'decimal:2','per_kg_rate'=>'decimal:2','free_over'=>'decimal:2','min_weight'=>'decimal:3','max_weight'=>'decimal:3','active'=>'boolean'];public function zone(){return $this->belongsTo(ShippingZone::class,'shipping_zone_id');}}