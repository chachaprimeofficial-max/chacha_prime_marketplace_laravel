<?php
namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PricingService {
 public function unitPrice(Product $product,float $quantity,string $customerType='b2c',?int $countryId=null): float {
  $tier=$product->pricingTiers()->where('customer_type',$customerType)->where('min_quantity','<=',$quantity)->where(fn($q)=>$q->whereNull('max_quantity')->orWhere('max_quantity','>=',$quantity))->orderByDesc('min_quantity')->first();
  if($tier) return (float)$tier->price;
  $countryId=$countryId ?: (int)session('marketplace_country_id',0);
  if($countryId){
   $marketplace=DB::table('product_marketplaces')->where('product_id',$product->id)->where('country_id',$countryId)->where('active',1)->first();
   if($marketplace) return (float)($customerType==='b2b' && $marketplace->wholesale_price!==null ? $marketplace->wholesale_price : $marketplace->retail_price);
  }
  return (float)$product->retail_price;
 }

 public function currency(Product $product,?int $countryId=null): string {
  $countryId=$countryId ?: (int)session('marketplace_country_id',0);
  if($countryId){
   $marketplace=DB::table('product_marketplaces')->where('product_id',$product->id)->where('country_id',$countryId)->where('active',1)->first();
   if($marketplace && $marketplace->currency) return strtoupper((string)$marketplace->currency);
   $country=DB::table('countries')->where('id',$countryId)->first();
   if($country && $country->currency_code && (int)$product->country_id===$countryId) return strtoupper((string)$country->currency_code);
  }
  return strtoupper((string)($product->currency ?: 'USD'));
 }

 public function stock(Product $product,?int $countryId=null): float {
  $countryId=$countryId ?: (int)session('marketplace_country_id',0);
  if($countryId){
   $marketplace=DB::table('product_marketplaces')->where('product_id',$product->id)->where('country_id',$countryId)->where('active',1)->first();
   if($marketplace && $marketplace->stock!==null) return (float)$marketplace->stock;
  }
  return (float)$product->stock;
 }
}