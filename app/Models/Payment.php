<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
 protected $fillable=['order_id','user_id','method_id','transaction_reference','amount','currency','status','gateway_response','paid_at'];
 protected $casts=['amount'=>'decimal:2','gateway_response'=>'array','paid_at'=>'datetime'];
 public function order(){return $this->belongsTo(Order::class);}
 public function user(){return $this->belongsTo(User::class);}
}