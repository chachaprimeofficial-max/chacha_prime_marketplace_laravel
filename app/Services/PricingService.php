<?php
namespace AppServices;
use AppModelsProduct;
class PricingService {
 public function unitPrice(Product $product,float $quantity,string $customerType='b2c'): float {
  $tier=$product->pricingTiers()->where('customer_type',$customerType)->where('min_quantity','<=',$quantity)->where(fn($q)=>$q->whereNull('max_quantity')->orWhere('max_quantity','>=',$quantity))->orderByDesc('min_quantity')->first();
  return (float)($tier?->price ?? $product->retail_price);
 }
}