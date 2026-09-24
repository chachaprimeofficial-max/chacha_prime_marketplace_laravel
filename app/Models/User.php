<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use Notifiable;
 protected $fillable=['name','email','password','role','status','phone','avatar','locale','country_id','totp_secret','two_factor_enabled'];
 protected $hidden=['password','remember_token','totp_secret'];
 protected function casts(): array {return ['email_verified_at'=>'datetime','password'=>'hashed','two_factor_enabled'=>'boolean'];}
 public function otpCodes(){return $this->hasMany(OtpCode::class);}
 public function vendor(){return $this->hasOne(Vendor::class);}
 public function country(){return $this->belongsTo(Country::class);}
 public function wallet(){return $this->hasOne(Wallet::class);}
}