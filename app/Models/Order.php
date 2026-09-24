<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
 protected $fillable=['user_id','country_id','order_number','status','payment_status','fulfillment_status','currency','subtotal','discount_total','shipping_total','tax_total','grand_total','shipping_address','billing_address','notes'];
 protected $casts=['shipping_address'=>'array','billing_address'=>'array','subtotal'=>'decimal:2','discount_total'=>'decimal:2','shipping_total'=>'decimal:2','tax_total'=>'decimal:2','grand_total'=>'decimal:2'];
 public function user(){return $this->belongsTo(User::class);}
 public function country(){return $this->belongsTo(Country::class);}
 public function items(){return $this->hasMany(OrderItem::class);}
 public function payments(){return $this->hasMany(Payment::class);}
}