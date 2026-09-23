<?php
namespace AppModels;
use IlluminateDatabaseEloquentModel;
class VirtualCard extends Model {
 protected $fillable=['user_id','card_token','display_number','last4','expiry_month','expiry_year','status','issued_at'];
 protected $hidden=['card_token'];
 protected $casts=['issued_at'=>'datetime'];
 public function user(){return $this->belongsTo(User::class);}
}