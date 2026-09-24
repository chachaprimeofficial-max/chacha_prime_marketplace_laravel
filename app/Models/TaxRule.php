<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TaxRule extends Model {protected $fillable=['country_id','vendor_id','category_id','name','rate','prices_include_tax','active'];protected $casts=['rate'=>'decimal:4','prices_include_tax'=>'boolean','active'=>'boolean'];public function country(){return $this->belongsTo(Country::class);}}