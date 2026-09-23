<?php
namespace AppModels;
use IlluminateDatabaseEloquentModel;
class Category extends Model {
 protected $fillable=['parent_id','name','slug','description','image','sort_order','status'];
 public function parent(){return $this->belongsTo(Category::class,'parent_id');}
 public function children(){return $this->hasMany(Category::class,'parent_id');}
 public function products(){return $this->hasMany(Product::class);}
}