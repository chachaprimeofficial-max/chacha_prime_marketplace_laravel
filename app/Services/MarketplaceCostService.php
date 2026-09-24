<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
class MarketplaceCostService {
 public function shippingForVendor(iterable $products,array $quantities,int $countryId,int $vendorId,float $subtotal): array {
  $zone=DB::table('shipping_zones')->where('country_id',$countryId)->where('active',1)->where(fn($q)=>$q->whereNull('vendor_id')->orWhere('vendor_id',$vendorId))->orderByRaw('vendor_id IS NULL')->first();
  if(!$zone)return ['amount'=>0,'method'=>null,'min_days'=>null,'max_days'=>null];
  $weight=0;foreach($products as $p)$weight+=max(0,(float)$p->weight)*max(1,(int)($quantities[$p->id]??1));
  $method=DB::table('shipping_methods')->where('shipping_zone_id',$zone->id)->where('active',1)->where(fn($q)=>$q->whereNull('min_weight')->orWhere('min_weight','<=',$weight))->where(fn($q)=>$q->whereNull('max_weight')->orWhere('max_weight','>=',$weight))->orderBy('base_rate')->first();
  if(!$method)return ['amount'=>0,'method'=>null,'min_days'=>null,'max_days'=>null];
  $amount=$method->rate_type==='free'?0:(($method->free_over!==null&&$subtotal>=(float)$method->free_over)?0:($method->rate_type==='weight'?(float)$method->base_rate+(float)$method->per_kg_rate*$weight:(float)$method->base_rate));
  return ['amount'=>round($amount,2),'method'=>$method->name,'min_days'=>$method->min_days,'max_days'=>$method->max_days];
 }
 public function taxForVendor(iterable $products,array $quantities,int $countryId,int $vendorId,float $subtotal):float {
  $tax=0;$pricing=app(PricingService::class);
  foreach($products as $p){$qty=max(1,(int)($quantities[$p->id]??1));$line=round($pricing->unitPrice($p,$qty,'b2c',$countryId)*$qty,2);$rule=DB::table('tax_rules')->where('country_id',$countryId)->where('active',1)->where(fn($q)=>$q->whereNull('vendor_id')->orWhere('vendor_id',$vendorId))->where(fn($q)=>$q->whereNull('category_id')->orWhere('category_id',$p->category_id))->orderByRaw('category_id IS NULL')->orderByRaw('vendor_id IS NULL')->first();if($rule&&!$rule->prices_include_tax)$tax+=$line*((float)$rule->rate/100);}
  return round($tax,2);
 }
}