<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShippingZone extends Model {protected $fillable=['country_id','vendor_id','name','active'];protected $casts=['active'=>'boolean'];public function country(){return $this->belongsTo(Country::class);}public function vendor(){return $this->belongsTo(Vendor::class);}public function methods(){return $this->hasMany(ShippingMethod::class);}}