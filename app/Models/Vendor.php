<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vendor extends Model {
 protected $fillable=['user_id','business_name','legal_name','country','registration_number','tax_number','status','verification_status','logo','description','commission_rate'];
 protected $casts=['commission_rate'=>'decimal:3'];
 public function user(){return $this->belongsTo(User::class);}
 public function products(){return $this->hasMany(Product::class);}
}