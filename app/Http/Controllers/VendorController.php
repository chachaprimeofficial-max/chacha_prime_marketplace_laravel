<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorController extends Controller
{
 private function vendor(Request $request): Vendor { return Vendor::where('user_id',$request->user()->id)->firstOrFail(); }

 public function dashboard(Request $request){
  $vendor=$this->vendor($request);
  $productIds=Product::where('vendor_id',$vendor->id)->pluck('id');
  $items=DB::table('order_items')->where('vendor_id',$vendor->id);
  $sales=(clone $items)->join('orders','orders.id','=','order_items.order_id')->whereIn('orders.payment_status',['paid'])->sum('order_items.subtotal');
  $categories=DB::table('categories')->where('status',1)->orderBy('name')->get();
  return view('vendor.dashboard',['vendor'=>$vendor,'categories'=>$categories,'stats'=>[
   'products'=>Product::where('vendor_id',$vendor->id)->count(),
   'published'=>Product::where('vendor_id',$vendor->id)->where('status','published')->count(),
   'pending'=>Product::where('vendor_id',$vendor->id)->where('status','pending')->count(),
   'orders'=>(clone $items)->distinct('order_id')->count('order_id'),
   'sales'=>$sales,
   'reviews'=>DB::table('reviews')->whereIn('product_id',$productIds)->count(),
   'low_stock'=>Product::where('vendor_id',$vendor->id)->where('stock','<=',10)->count(),
  ],'recentProducts'=>Product::where('vendor_id',$vendor->id)->latest()->limit(8)->get()]);
 }

 public function products(Request $request){
  $vendor=$this->vendor($request); $q=trim((string)$request->get('q','')); $status=$request->get('status');
  $query=Product::where('vendor_id',$vendor->id)->when($q,fn($x)=>$x->where(fn($y)=>$y->where('name','like','%'.$q.'%')->orWhere('sku','like','%'.$q.'%')))->when($status,fn($x)=>$x->where('status',$status));
  return view('vendor.products',['vendor'=>$vendor,'products'=>$query->latest()->paginate(15)->withQueryString(),'categories'=>DB::table('categories')->where('status',1)->orderBy('name')->get(),'brands'=>DB::table('brands')->where('status',1)->orderBy('name')->get(),'catalogStats'=>['total'=>Product::where('vendor_id',$vendor->id)->count(),'published'=>Product::where('vendor_id',$vendor->id)->where('status','published')->count(),'pending'=>Product::where('vendor_id',$vendor->id)->where('status','pending')->count(),'low'=>Product::where('vendor_id',$vendor->id)->where('stock','<=',10)->count()],'q'=>$q,'status'=>$status]);
 }

 public function storeProduct(Request $request){
  $vendor=$this->vendor($request);
  $d=$request->validate(['name'=>'required|string|max:220','category_id'=>'nullable|exists:categories,id','brand_id'=>'nullable|exists:brands,id','short_description'=>'nullable|string|max:1000','description'=>'nullable|string','retail_price'=>'required|numeric|min:0','cost_price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','stock'=>'required|numeric|min:0','sku'=>'nullable|string|max:100']);
  $d['vendor_id']=$vendor->id;$d['slug']=Str::slug($d['name']).'-'.Str::lower(Str::random(6));$d['sku']=$d['sku'] ?: 'CP-'.strtoupper(Str::random(8));$d['status']='pending';$d['stock_status']=$d['stock']>0?'in_stock':'out_of_stock';
  $product=Product::create($d);app(AuditLogService::class)->log('vendor.product.created','Product',$product->id,['vendor_id'=>$vendor->id,'name'=>$product->name]);return back()->with('success','Product submitted for admin approval.');
 }

 public function updateProduct(Request $request,int $id){
  $vendor=$this->vendor($request);$p=Product::where('vendor_id',$vendor->id)->findOrFail($id);
  $d=$request->validate(['name'=>'required|string|max:220','category_id'=>'nullable|exists:categories,id','brand_id'=>'nullable|exists:brands,id','short_description'=>'nullable|string|max:1000','description'=>'nullable|string','retail_price'=>'required|numeric|min:0','cost_price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','stock'=>'required|numeric|min:0','sku'=>'required|string|max:100']);
  $d['stock_status']=$d['stock']>0?'in_stock':'out_of_stock';$before=$p->only(['name','retail_price','stock','status']);$p->update($d);app(AuditLogService::class)->log('vendor.product.updated','Product',$p->id,['before'=>$before,'after'=>$d]);return back()->with('success','Product updated.');
 }

 public function editProduct(Request $request,int $id){$vendor=$this->vendor($request);$product=Product::where('vendor_id',$vendor->id)->findOrFail($id);$categories=DB::table('categories')->where('status',1)->orderBy('name')->get();return view('vendor.product-edit',compact('vendor','product','categories'));}

 public function deleteProduct(Request $request,int $id){$vendor=$this->vendor($request);$p=Product::where('vendor_id',$vendor->id)->findOrFail($id);$p->update(['status'=>'archived']);app(AuditLogService::class)->log('vendor.product.archived','Product',$p->id,['vendor_id'=>$vendor->id]);return back()->with('success','Product archived.');}

 public function pricing(Request $request){$vendor=$this->vendor($request);$products=Product::where('vendor_id',$vendor->id)->latest()->paginate(20);$tiers=DB::table('pricing_tiers')->join('products','products.id','=','pricing_tiers.product_id')->where('products.vendor_id',$vendor->id)->select('pricing_tiers.*','products.name')->latest('pricing_tiers.id')->get();return view('vendor.pricing',compact('vendor','products','tiers'));}

 public function storePricing(Request $request){
  $vendor=$this->vendor($request);
  $d=$request->validate(['product_id'=>'required|integer','customer_type'=>'required|in:b2b,b2c','min_quantity'=>'required|numeric|min:1','max_quantity'=>'nullable|numeric|gte:min_quantity','price'=>'required|numeric|min:0','discount_percent'=>'nullable|numeric|min:0|max:100']);
  abort_unless(Product::where('vendor_id',$vendor->id)->where('id',$d['product_id'])->exists(),403);DB::table('pricing_tiers')->insert($d+['created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Pricing tier saved.');
 }
 public function deletePricing(Request $request,int $id){$vendor=$this->vendor($request);$tier=DB::table('pricing_tiers')->join('products','products.id','=','pricing_tiers.product_id')->where('pricing_tiers.id',$id)->where('products.vendor_id',$vendor->id)->select('pricing_tiers.id')->first();abort_unless($tier,404);DB::table('pricing_tiers')->where('id',$id)->delete();return back()->with('success','Pricing tier removed.');}

 public function inventory(Request $request){$vendor=$this->vendor($request);return view('vendor.inventory',['vendor'=>$vendor,'products'=>Product::where('vendor_id',$vendor->id)->orderBy('stock')->paginate(25)]);}

 public function analytics(Request $request){$vendor=$this->vendor($request);$items=DB::table('order_items')->where('vendor_id',$vendor->id);$sales=(clone $items)->join('orders','orders.id','=','order_items.order_id')->where('orders.payment_status','paid')->sum('order_items.subtotal');return view('vendor.analytics',['vendor'=>$vendor,'stats'=>['orders'=>(clone $items)->distinct('order_id')->count('order_id'),'sales'=>$sales,'pending'=>(clone $items)->join('orders','orders.id','=','order_items.order_id')->where('orders.status','pending')->distinct('order_id')->count('order_id'),'delivered'=>(clone $items)->join('orders','orders.id','=','order_items.order_id')->where('orders.fulfillment_status','delivered')->distinct('order_id')->count('order_id')]]);}

 public function shipping(Request $request){$vendor=$this->vendor($request);$methods=DB::table('shipping_methods')->where('enabled',1)->orderBy('name')->get();return view('vendor.shipping',compact('vendor','methods'));}

 public function coupons(Request $request){$vendor=$this->vendor($request);$coupons=DB::table('coupons')->latest()->paginate(20);return view('vendor.coupons',compact('vendor','coupons'));}

 public function storeCoupon(Request $request){$this->vendor($request);$d=$request->validate(['code'=>'required|string|max:80|unique:coupons,code','type'=>'required|in:fixed,percent','value'=>'required|numeric|min:0','min_order'=>'nullable|numeric|min:0','max_discount'=>'nullable|numeric|min:0','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at','usage_limit'=>'nullable|integer|min:1']);DB::table('coupons')->insert($d+['used_count'=>0,'status'=>1]);return back()->with('success','Coupon created.');}

 public function groupBuying(Request $request){$vendor=$this->vendor($request);$campaigns=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('products.vendor_id',$vendor->id)->select('group_buying_campaigns.*','products.name')->latest('group_buying_campaigns.id')->paginate(20);$products=Product::where('vendor_id',$vendor->id)->where('status','published')->get();return view('vendor.group-buying',compact('vendor','campaigns','products'));}
 public function storeGroupBuying(Request $request){$vendor=$this->vendor($request);$d=$request->validate(['product_id'=>'required|integer','title'=>'required|string|max:220','target_participants'=>'required|integer|min:2','offer_price'=>'required|numeric|min:0','currency'=>'required|string|size:3','starts_at'=>'required|date','ends_at'=>'required|date|after:starts_at']);abort_unless(Product::where('vendor_id',$vendor->id)->where('id',$d['product_id'])->exists(),403);DB::table('group_buying_campaigns')->insert($d+['status'=>'draft','created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Group campaign created.');}

 public function liveCommerce(Request $request){$vendor=$this->vendor($request);$streams=DB::table('live_streams')->where('vendor_id',$vendor->id)->latest()->paginate(20);$products=Product::where('vendor_id',$vendor->id)->where('status','published')->get();return view('vendor.live-commerce',compact('vendor','streams','products'));}
 public function storeLive(Request $request){$vendor=$this->vendor($request);$d=$request->validate(['title'=>'required|string|max:220','youtube_url'=>'required|url|max:500','youtube_video_id'=>'nullable|string|max:100','status'=>'required|in:scheduled,live,ended','starts_at'=>'nullable|date']);$d['vendor_id']=$vendor->id;$d['youtube_video_id']=$d['youtube_video_id'] ?: $this->youtubeId($d['youtube_url']);$d['viewer_count']=0;DB::table('live_streams')->insert($d+['created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Live stream saved.');}
 private function youtubeId(string $url): ?string { $parts=parse_url($url); if(!empty($parts['query'])) parse_str($parts['query'],$q); else $q=[]; if(!empty($q['v'])) return preg_replace('/[^A-Za-z0-9_-]/','',$q['v']); if(!empty($parts['path']) && preg_match('~/(?:live/|embed/|shorts/)?([A-Za-z0-9_-]{6,})~',$parts['path'],$m)) return $m[1]; return null; }

 public function ai(Request $request){$vendor=$this->vendor($request);$logs=DB::table('ai_logs')->where('user_id',$request->user()->id)->latest()->paginate(20);return view('vendor.ai',compact('vendor','logs'));}
 public function payouts(Request $request){$vendor=$this->vendor($request);$wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();$transactions=$wallet?DB::table('wallet_transactions')->where('wallet_id',$wallet->id)->latest()->paginate(20):collect();return view('vendor.payouts',compact('vendor','wallet','transactions'));}
 public function orders(Request $request){$vendor=$this->vendor($request);$orders=DB::table('orders')->join('order_items','orders.id','=','order_items.order_id')->where('order_items.vendor_id',$vendor->id)->select('orders.id','orders.order_number','orders.status','orders.payment_status','orders.fulfillment_status','orders.grand_total','orders.currency')->distinct()->latest('orders.id')->paginate(20);return view('vendor.orders',compact('vendor','orders'));}
 public function updateOrder(Request $request,int $id){$vendor=$this->vendor($request);$d=$request->validate(['status'=>'required|in:pending,processing,cancelled','fulfillment_status'=>'required|in:unfulfilled,processing,shipped,delivered']);$owns=DB::table('order_items')->where('vendor_id',$vendor->id)->where('order_id',$id)->exists();abort_unless($owns,404);DB::table('orders')->where('id',$id)->update(['status'=>$d['status'],'fulfillment_status'=>$d['fulfillment_status'],'updated_at'=>now()]);return back()->with('success','Order updated.');}
 public function wallet(Request $request){$vendor=$this->vendor($request);$wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();return view('vendor.wallet',compact('vendor','wallet'));}
 public function reviews(Request $request){$vendor=$this->vendor($request);$productIds=Product::where('vendor_id',$vendor->id)->pluck('id');return view('vendor.reviews',['vendor'=>$vendor,'reviews'=>DB::table('reviews')->whereIn('product_id',$productIds)->latest()->paginate(20)]);}
}