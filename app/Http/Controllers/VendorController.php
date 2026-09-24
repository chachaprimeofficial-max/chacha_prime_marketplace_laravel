<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use App\Models\Vendor;
use App\Services\AuditLogService;
use App\Services\SubscriptionLimitService;
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
  try { app(SubscriptionLimitService::class)->assertWithin($request->user()->id,'products_created',1); } catch (\Throwable $e) { return back()->withErrors(['subscription'=>$e->getMessage()])->withInput(); }
  $d=$request->validate(['name'=>'required|string|max:220','country_id'=>'required|exists:countries,id','category_id'=>'nullable|exists:categories,id','brand_id'=>'nullable|exists:brands,id','short_description'=>'nullable|string|max:1000','description'=>'nullable|string','highlights'=>'nullable|string|max:10000','specifications'=>'nullable|string|max:15000','shipping_info'=>'nullable|string|max:5000','features'=>'nullable|string|max:10000','additional_details'=>'nullable|string|max:10000','return_policy'=>'nullable|string|max:500','gift_option'=>'nullable|boolean','fulfillment_type'=>'required|in:seller,marketplace','sold_by_type'=>'required|in:seller,marketplace_fba','video_urls'=>'nullable|string|max:10000','retail_price'=>'required|numeric|min:0','wholesale_price'=>'nullable|numeric|min:0','factory_price'=>'nullable|numeric|min:0','cost_price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','stock'=>'required|numeric|min:0','sku'=>'nullable|string|max:100','images'=>'nullable|array|max:8','images.*'=>'image|mimes:jpg,jpeg,png,webp|max:4096','videos'=>'nullable|array|max:4','videos.*'=>'file|mimes:mp4,webm,mov|max:51200','variations'=>'nullable|string|max:10000']);
  $images=$request->file('images',[]);$videos=$request->file('videos',[]);$variations=$request->input('variations');unset($d['images'],$d['videos']);$d['vendor_id']=$vendor->id;$d['slug']=Str::slug($d['name']).'-'.Str::lower(Str::random(6));$d['sku']=$d['sku'] ?: 'CP-'.strtoupper(Str::random(8));$d['status']='pending';$d['stock_status']=$d['stock']>0?'in_stock':'out_of_stock';
  $product=Product::create($d);$this->ensureIdentifier($product);app(SubscriptionLimitService::class)->consume($request->user()->id,'products_created',1);$this->saveProductImages($request,$product,$images);$this->saveProductVideos($product,$videos);$this->saveProductVariants($product,$request->input('variations'));app(AuditLogService::class)->log('vendor.product.created','Product',$product->id,['vendor_id'=>$vendor->id,'name'=>$product->name]);return back()->with('success','Product submitted for admin approval.');
 }

 public function updateProduct(Request $request,int $id){
  $vendor=$this->vendor($request);$p=Product::where('vendor_id',$vendor->id)->findOrFail($id);
  $d=$request->validate(['name'=>'required|string|max:220','category_id'=>'nullable|exists:categories,id','brand_id'=>'nullable|exists:brands,id','short_description'=>'nullable|string|max:1000','description'=>'nullable|string','highlights'=>'nullable|string|max:10000','specifications'=>'nullable|string|max:15000','shipping_info'=>'nullable|string|max:5000','features'=>'nullable|string|max:10000','additional_details'=>'nullable|string|max:10000','return_policy'=>'nullable|string|max:500','gift_option'=>'nullable|boolean','fulfillment_type'=>'required|in:seller,marketplace','sold_by_type'=>'required|in:seller,marketplace_fba','video_urls'=>'nullable|string|max:10000','retail_price'=>'required|numeric|min:0','wholesale_price'=>'nullable|numeric|min:0','factory_price'=>'nullable|numeric|min:0','cost_price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','stock'=>'required|numeric|min:0','sku'=>'required|string|max:100','images'=>'nullable|array|max:8','images.*'=>'image|mimes:jpg,jpeg,png,webp|max:4096','videos'=>'nullable|array|max:4','videos.*'=>'file|mimes:mp4,webm,mov|max:51200','variations'=>'nullable|string|max:10000']);
  $images=$request->file('images',[]);$videos=$request->file('videos',[]);$variations=$request->input('variations');unset($d['images'],$d['videos'],$d['variations']);$d['stock_status']=$d['stock']>0?'in_stock':'out_of_stock';$before=$p->only(['name','retail_price','stock','status']);$p->update($d);$this->ensureIdentifier($p);$this->saveProductImages($request,$p,$images);$this->saveProductVideos($p,$videos);$this->saveProductVariants($p,$variations);app(AuditLogService::class)->log('vendor.product.updated','Product',$p->id,['before'=>$before,'after'=>$d]);return back()->with('success','Product updated.');
 }

 private function ensureIdentifier(Product $product): void { if(DB::table('product_identifiers')->where('product_id',$product->id)->exists()) return; DB::table('product_identifiers')->insert(['product_id'=>$product->id,'product_code'=>'CP-PROD-'.str_pad((string)$product->id,8,'0',STR_PAD_LEFT),'internal_sku'=>$product->sku,'barcode_value'=>'CP'.str_pad((string)$product->id,12,'0',STR_PAD_LEFT),'barcode_type'=>'CODE128','qr_token'=>bin2hex(random_bytes(20)),'created_at'=>now(),'updated_at'=>now()]); }

 private function saveProductImages(Request $request, Product $product, array $files=[]): void
 {
  if(!$files) $files=$request->file('images',[]);
  if(!$files) return;
  $hasPrimary=ProductImage::where('product_id',$product->id)->where('is_primary',1)->exists();
  foreach($files as $file){
   $path=$file->store('products','public');
   ProductImage::create([
    'product_id'=>$product->id,
    'path'=>$path,
    'alt_text'=>$product->name,
    'sort_order'=>((int)ProductImage::where('product_id',$product->id)->max('sort_order'))+1,
    'is_primary'=>$hasPrimary?0:1,
   ]);
   $hasPrimary=true;
  }
 }

 private function saveProductVariants(Product $product,?string $raw): void { if(!$raw) return; foreach(preg_split('/\r\n|\r|\n/',trim($raw),-1,PREG_SPLIT_NO_EMPTY) as $line){ $p=array_map('trim',explode('|',$line)); if(count($p)<4) continue; $name=$p[0]; $sku=$p[1] ?: 'CPV-'.strtoupper(Str::random(7)); $price=(float)$p[2]; $stock=(float)$p[3]; DB::table('product_variants')->insert(['product_id'=>$product->id,'sku'=>$sku,'name'=>$name,'attributes'=>json_encode(['label'=>$name]),'price'=>$price,'stock'=>$stock,'status'=>1]); } }

 private function saveProductVideos(Product $product,array $files=[]): void { foreach($files as $file){ $path=$file->store('products/videos','public'); ProductVideo::create(['product_id'=>$product->id,'path'=>$path,'title'=>$product->name,'sort_order'=>((int)ProductVideo::where('product_id',$product->id)->max('sort_order'))+1]); } }

 public function editProduct(Request $request,int $id){$vendor=$this->vendor($request);$product=Product::with(['images','videos','variants'])->where('vendor_id',$vendor->id)->findOrFail($id);$categories=DB::table('categories')->where('status',1)->orderBy('name')->get();$countries=DB::table('countries')->where('active',1)->orderBy('sort_order')->orderBy('name')->get();return view('vendor.product-edit',compact('vendor','product','categories','countries'));}

 public function deleteProduct(Request $request,int $id){$vendor=$this->vendor($request);$p=Product::where('vendor_id',$vendor->id)->findOrFail($id);$p->update(['status'=>'archived']);app(AuditLogService::class)->log('vendor.product.archived','Product',$p->id,['vendor_id'=>$vendor->id]);return back()->with('success','Product archived.');}

 public function pricing(Request $request){$vendor=$this->vendor($request);$products=Product::where('vendor_id',$vendor->id)->latest()->paginate(20);$tiers=DB::table('pricing_tiers')->join('products','products.id','=','pricing_tiers.product_id')->where('products.vendor_id',$vendor->id)->select('pricing_tiers.*','products.name')->latest('pricing_tiers.id')->get();return view('vendor.pricing',compact('vendor','products','tiers'));}

 public function storePricing(Request $request){
  $vendor=$this->vendor($request);
  $d=$request->validate(['product_id'=>'required|integer','customer_type'=>'required|in:b2b,b2c','min_quantity'=>'required|numeric|min:1','max_quantity'=>'nullable|numeric|gte:min_quantity','price'=>'required|numeric|min:0','discount_percent'=>'nullable|numeric|min:0|max:100']);
  abort_unless(Product::where('vendor_id',$vendor->id)->where('id',$d['product_id'])->exists(),403);DB::table('pricing_tiers')->insert($d+['created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Pricing tier saved.');
 }
 public function deletePricing(Request $request,int $id){$vendor=$this->vendor($request);$tier=DB::table('pricing_tiers')->join('products','products.id','=','pricing_tiers.product_id')->where('pricing_tiers.id',$id)->where('products.vendor_id',$vendor->id)->select('pricing_tiers.id')->first();abort_unless($tier,404);DB::table('pricing_tiers')->where('id',$id)->delete();return back()->with('success','Pricing tier removed.');}

 public function inventory(Request $request){$vendor=$this->vendor($request);return view('vendor.inventory',['vendor'=>$vendor,'products'=>Product::where('vendor_id',$vendor->id)->orderBy('stock')->paginate(25)]);}

 public function analytics(Request $request){
  $vendor=$this->vendor($request);
  $days=(int)$request->get('days',30); if(!in_array($days,[7,30,90],true))$days=30;
  $from=now()->subDays($days-1)->startOfDay(); $to=now()->endOfDay();
  $base=DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.vendor_id',$vendor->id)->whereBetween('orders.created_at',[$from,$to]);
  $sales=(clone $base)->where('orders.payment_status','paid')->sum('order_items.subtotal');
  $orders=(clone $base)->distinct('orders.id')->count('orders.id');
  $pending=(clone $base)->where('orders.status','pending')->distinct('orders.id')->count('orders.id');
  $delivered=(clone $base)->where('orders.fulfillment_status','delivered')->distinct('orders.id')->count('orders.id');
  $daily=(clone $base)->where('orders.payment_status','paid')->selectRaw('DATE(orders.created_at) day,SUM(order_items.subtotal) sales')->groupByRaw('DATE(orders.created_at)')->orderBy('day')->get();
  $topProducts=(clone $base)->where('orders.payment_status','paid')->select('order_items.product_id','order_items.product_name')->selectRaw('SUM(order_items.quantity) quantity,SUM(order_items.subtotal) sales')->groupBy('order_items.product_id','order_items.product_name')->orderByDesc('sales')->limit(8)->get();
  return view('vendor.analytics',compact('vendor','days','from','to','daily','topProducts')+['stats'=>['orders'=>$orders,'sales'=>$sales,'pending'=>$pending,'delivered'=>$delivered]]);
 }

 public function accountHealth(Request $request){
  $vendor=$this->vendor($request);$productQ=Product::where('vendor_id',$vendor->id);
  $totalProducts=(clone $productQ)->count();$published=(clone $productQ)->where('status','published')->count();$pending=(clone $productQ)->where('status','pending')->count();$lowStock=(clone $productQ)->where('stock','<=',10)->count();
  $items=DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.vendor_id',$vendor->id);
  $totalOrders=(clone $items)->distinct('orders.id')->count('orders.id');$cancelled=(clone $items)->where('orders.status','cancelled')->distinct('orders.id')->count('orders.id');$delivered=(clone $items)->where('orders.fulfillment_status','delivered')->distinct('orders.id')->count('orders.id');
  $productIds=(clone $productQ)->pluck('id');$reviews=DB::table('reviews')->whereIn('product_id',$productIds);$reviewCount=(clone $reviews)->count();$avgRating=round((float)((clone $reviews)->avg('rating')??0),2);
  return view('vendor.account-health',compact('vendor','totalProducts','published','pending','lowStock','totalOrders','cancelled','delivered','reviewCount','avgRating'));
 }

 public function reports(Request $request){
  $vendor=$this->vendor($request);$days=(int)$request->get('days',30);if(!in_array($days,[7,30,90],true))$days=30;$from=now()->subDays($days-1)->startOfDay();
  $base=DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.vendor_id',$vendor->id)->where('orders.created_at','>=',$from);
  $rows=(clone $base)->selectRaw('DATE(orders.created_at) day,COUNT(DISTINCT orders.id) orders,SUM(order_items.quantity) units,SUM(CASE WHEN orders.payment_status="paid" THEN order_items.subtotal ELSE 0 END) sales')->groupByRaw('DATE(orders.created_at)')->orderByDesc('day')->get();
  return view('vendor.reports',compact('vendor','days','rows'));
 }

 public function shipping(Request $request){$vendor=$this->vendor($request);$methods=DB::table('shipping_methods')->where('enabled',1)->orderBy('name')->get();return view('vendor.shipping',compact('vendor','methods'));}

 public function coupons(Request $request){$vendor=$this->vendor($request);$coupons=DB::table('coupons')->where('vendor_id',$vendor->id)->latest()->paginate(20)->withQueryString();return view('vendor.coupons',compact('vendor','coupons'));}

 public function storeCoupon(Request $request){$vendor=$this->vendor($request);$d=$request->validate(['code'=>'required|string|max:80|unique:coupons,code','type'=>'required|in:fixed,percent','value'=>'required|numeric|min:0','min_order'=>'nullable|numeric|min:0','max_discount'=>'nullable|numeric|min:0','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at','usage_limit'=>'nullable|integer|min:1']);$d['code']=strtoupper(trim($d['code']));$d['vendor_id']=$vendor->id;$d['used_count']=0;$d['status']=1;$d['created_at']=now();$d['updated_at']=now();DB::table('coupons')->insert($d);return back()->with('success','Coupon created.');}

 public function groupBuying(Request $request){$vendor=$this->vendor($request);$campaigns=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('products.vendor_id',$vendor->id)->select('group_buying_campaigns.*','products.name')->selectSub(DB::table('group_buying_participants')->selectRaw('COALESCE(SUM(quantity),0)')->whereColumn('campaign_id','group_buying_campaigns.id')->whereIn('status',['reserved','confirmed']),'participants_count')->latest('group_buying_campaigns.id')->paginate(20);$products=Product::where('vendor_id',$vendor->id)->where('status','published')->get();return view('vendor.group-buying',compact('vendor','campaigns','products'));}
 public function storeGroupBuying(Request $request){$vendor=$this->vendor($request);$d=$request->validate(['product_id'=>'required|integer','title'=>'required|string|max:220','target_participants'=>'required|integer|min:2','offer_price'=>'required|numeric|min:0','currency'=>'required|string|size:3','starts_at'=>'required|date','ends_at'=>'required|date|after:starts_at']);abort_unless(Product::where('vendor_id',$vendor->id)->where('id',$d['product_id'])->exists(),403);DB::table('group_buying_campaigns')->insert($d+['status'=>'draft','created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Group campaign created as draft. Review it, then activate it.');}
 public function toggleGroupBuying(Request $request,int $id){$vendor=$this->vendor($request);$campaign=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('group_buying_campaigns.id',$id)->where('products.vendor_id',$vendor->id)->select('group_buying_campaigns.*')->first();abort_unless($campaign,404);$next=$campaign->status==='active'?'draft':'active';DB::table('group_buying_campaigns')->where('id',$id)->update(['status'=>$next,'updated_at'=>now()]);return back()->with('success','Group campaign '.$next.'.');}

 public function liveCommerce(Request $request){$vendor=$this->vendor($request);$streams=DB::table('live_streams')->where('vendor_id',$vendor->id)->latest()->paginate(20);$products=Product::where('vendor_id',$vendor->id)->where('status','published')->get();foreach($streams as $s){$s->pinned_products=DB::table('live_products')->join('products','products.id','=','live_products.product_id')->where('live_products.live_stream_id',$s->id)->select('products.id','products.name','products.retail_price','products.currency','live_products.pin_order')->orderBy('live_products.pin_order')->get();}return view('vendor.live-commerce',compact('vendor','streams','products'));}
 public function toggleLive(Request $request,int $id){$vendor=$this->vendor($request);$stream=DB::table('live_streams')->where('id',$id)->where('vendor_id',$vendor->id)->firstOrFail();$next=$stream->status==='live'?'scheduled':'live';DB::table('live_streams')->where('id',$id)->update(['status'=>$next,'updated_at'=>now()]);return back()->with('success','Live stream status changed to '.$next.'.');}
 public function pinLiveProduct(Request $request,int $id){$vendor=$this->vendor($request);$d=$request->validate(['product_id'=>'required|integer']);$stream=DB::table('live_streams')->where('id',$id)->where('vendor_id',$vendor->id)->firstOrFail();abort_unless(Product::where('id',$d['product_id'])->where('vendor_id',$vendor->id)->where('status','published')->exists(),403);$exists=DB::table('live_products')->where('live_stream_id',$id)->where('product_id',$d['product_id'])->exists();if(!$exists){$next=(int)DB::table('live_products')->where('live_stream_id',$id)->max('pin_order')+1;DB::table('live_products')->insert(['live_stream_id'=>$id,'product_id'=>$d['product_id'],'pin_order'=>$next]);}return back()->with('success','Product pinned to live stream.');}
 public function unpinLiveProduct(Request $request,int $id,int $productId){$vendor=$this->vendor($request);$stream=DB::table('live_streams')->where('id',$id)->where('vendor_id',$vendor->id)->firstOrFail();DB::table('live_products')->where('live_stream_id',$stream->id)->where('product_id',$productId)->delete();return back()->with('success','Product removed from live stream.');}
 public function storeLive(Request $request){$vendor=$this->vendor($request);$d=$request->validate(['title'=>'required|string|max:220','youtube_url'=>'required|url|max:500','youtube_video_id'=>'nullable|string|max:100','status'=>'required|in:scheduled,live,ended','starts_at'=>'nullable|date']);$d['vendor_id']=$vendor->id;$d['youtube_video_id']=$d['youtube_video_id'] ?: $this->youtubeId($d['youtube_url']);$d['viewer_count']=0;DB::table('live_streams')->insert($d+['created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Live stream saved.');}
 private function youtubeId(string $url): ?string { $parts=parse_url($url); if(!empty($parts['query'])) parse_str($parts['query'],$q); else $q=[]; if(!empty($q['v'])) return preg_replace('/[^A-Za-z0-9_-]/','',$q['v']); if(!empty($parts['path']) && preg_match('~/(?:live/|embed/|shorts/)?([A-Za-z0-9_-]{6,})~',$parts['path'],$m)) return $m[1]; return null; }

 public function aiProductBuilder(Request $request){$data=$request->validate(['input'=>'required|string|max:2000']);try{app(SubscriptionLimitService::class)->assertWithin($request->user()->id,'ai_used',1);}catch(\Throwable $e){return response()->json(['ok'=>false,'message'=>$e->getMessage()],429);} $prompt="Return JSON only with keys title,short_description,highlights,description,specifications,features,seo_title,seo_description,keywords,category_suggestion. Create marketplace product content from: ".$data['input'];$raw=app(\App\Services\GeminiService::class)->ask($prompt);app(SubscriptionLimitService::class)->consume($request->user()->id,'ai_used',1);DB::table('ai_logs')->insert(['user_id'=>$request->user()->id,'context'=>'vendor_product_builder','provider'=>'gemini','model'=>config('services.gemini.model'),'prompt'=>$data['input'],'response'=>$raw,'created_at'=>now()]);return response()->json(['ok'=>true,'data'=>json_decode($raw,true),'raw'=>$raw]);}
 public function ai(Request $request){$vendor=$this->vendor($request);$logs=DB::table('ai_logs')->where('user_id',$request->user()->id)->latest()->paginate(20);return view('vendor.ai',compact('vendor','logs'));}
 public function requestPayout(Request $request){
  $vendor=$this->vendor($request);
  $data=$request->validate(['amount'=>'required|numeric|min:1','method'=>'nullable|string|max:60','destination'=>'nullable|string|max:190']);
  $wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();
  abort_unless($wallet,422,'Marketplace wallet is not available.');
  $amount=(float)$data['amount'];
  DB::transaction(function()use($wallet,$vendor,$amount,$data,$request){
   $locked=DB::table('wallets')->where('id',$wallet->id)->lockForUpdate()->first();
   if((float)$locked->balance<$amount) abort(422,'Insufficient wallet balance.');
   $newBalance=(float)$locked->balance-$amount;
   DB::table('wallets')->where('id',$locked->id)->update(['balance'=>$newBalance,'updated_at'=>now()]);
   $payoutId=DB::table('vendor_payouts')->insertGetId(['vendor_id'=>$vendor->id,'wallet_id'=>$locked->id,'amount'=>$amount,'currency'=>$locked->currency ?? 'USD','method'=>$data['method']??null,'destination'=>$data['destination']??null,'status'=>'requested','notes'=>'Amount reserved from seller wallet at payout request.','created_at'=>now(),'updated_at'=>now()]);
   DB::table('wallet_transactions')->insert(['wallet_id'=>$locked->id,'type'=>'debit','amount'=>$amount,'balance_after'=>$newBalance,'reference_type'=>'vendor_payout','reference_id'=>$payoutId,'description'=>'Payout amount reserved','created_at'=>now()]);
  });
  return back()->with('success','Payout request submitted and the amount has been reserved.');
 }
 public function payouts(Request $request){$vendor=$this->vendor($request);$wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();$transactions=$wallet?DB::table('wallet_transactions')->where('wallet_id',$wallet->id)->latest()->paginate(20):collect();return view('vendor.payouts',compact('vendor','wallet','transactions'));}
 public function orders(Request $request){
  $vendor=$this->vendor($request); $q=trim((string)$request->get('q','')); $status=$request->get('status'); $payment=$request->get('payment'); $fulfillment=$request->get('fulfillment');
  $base=DB::table('orders')->join('order_items','orders.id','=','order_items.order_id')->where('order_items.vendor_id',$vendor->id);
  $query=(clone $base)->when($q,fn($x)=>$x->where('orders.order_number','like','%'.$q.'%'))->when($status,fn($x)=>$x->where('orders.status',$status))->when($payment,fn($x)=>$x->where('orders.payment_status',$payment))->when($fulfillment,fn($x)=>$x->where('orders.fulfillment_status',$fulfillment))->select('orders.id','orders.order_number','orders.status','orders.payment_status','orders.fulfillment_status','orders.grand_total','orders.currency','orders.created_at')->distinct();
  return view('vendor.orders',['vendor'=>$vendor,'orders'=>$query->latest('orders.id')->paginate(20)->withQueryString(),'orderStats'=>[
   'total'=>(clone $base)->distinct('orders.id')->count('orders.id'),
   'pending'=>(clone $base)->where('orders.status','pending')->distinct('orders.id')->count('orders.id'),
   'processing'=>(clone $base)->where('orders.status','processing')->distinct('orders.id')->count('orders.id'),
   'paid'=>(clone $base)->where('orders.payment_status','paid')->distinct('orders.id')->count('orders.id'),
   'shipped'=>(clone $base)->where('orders.fulfillment_status','shipped')->distinct('orders.id')->count('orders.id'),
   'delivered'=>(clone $base)->where('orders.fulfillment_status','delivered')->distinct('orders.id')->count('orders.id'),
  ],'q'=>$q,'status'=>$status,'payment'=>$payment,'fulfillment'=>$fulfillment]);
 }
 public function updateOrder(Request $request,int $id){
  $vendor=$this->vendor($request);
  $d=$request->validate(['status'=>'required|in:pending,processing,cancelled','fulfillment_status'=>'required|in:unfulfilled,processing,shipped,delivered','tracking_number'=>'nullable|string|max:150']);
  $owns=DB::table('order_items')->where('vendor_id',$vendor->id)->where('order_id',$id)->exists();abort_unless($owns,404);
  DB::transaction(function()use($vendor,$id,$d){
   $order=DB::table('orders')->where('id',$id)->lockForUpdate()->first();abort_unless($order,404);
   if($d['status']==='cancelled' && $order->payment_status==='paid') abort(422,'Paid orders require the refund workflow before cancellation.');
   DB::table('orders')->where('id',$id)->update(['status'=>$d['status'],'fulfillment_status'=>$d['fulfillment_status'],'updated_at'=>now()]);
   if(in_array($d['fulfillment_status'],['shipped','delivered'],true)){
    $method=DB::table('shipping_methods')->where('enabled',1)->orderByRaw("CASE WHEN code='manual' THEN 0 ELSE 1 END")->first();abort_unless($method,422,'No enabled shipping method is available.');
    $shipment=DB::table('shipments')->where('order_id',$id)->where('vendor_id',$vendor->id)->lockForUpdate()->first();
    $status=$d['fulfillment_status']==='delivered'?'delivered':'shipped';
    $tracking=$d['tracking_number']??($shipment->tracking_number??null);
    if($shipment) DB::table('shipments')->where('id',$shipment->id)->update(['shipping_method_id'=>$method->id,'tracking_number'=>$tracking,'status'=>$status,'shipped_at'=>$shipment->shipped_at ?: ($status==='shipped'?now():null),'delivered_at'=>$status==='delivered'?now():$shipment->delivered_at,'updated_at'=>now()]);
    else DB::table('shipments')->insert(['order_id'=>$id,'vendor_id'=>$vendor->id,'shipping_method_id'=>$method->id,'tracking_number'=>$tracking,'status'=>$status,'shipping_cost'=>0,'shipped_at'=>$status==='shipped'?now():now(),'delivered_at'=>$status==='delivered'?now():null,'created_at'=>now(),'updated_at'=>now()]);
   }
  });
  return back()->with('success','Order and shipment status updated.');
 }
 public function returns(Request $request){
  $vendor=$this->vendor($request);
  $returns=DB::table('return_requests')->join('orders','orders.id','=','return_requests.order_id')->join('users','users.id','=','return_requests.customer_id')->where('return_requests.vendor_id',$vendor->id)->select('return_requests.*','orders.order_number','users.name as customer_name')->latest('return_requests.id')->paginate(20);
  return view('vendor.returns',compact('vendor','returns'));
 }
 public function updateReturn(Request $request,int $id){
  $vendor=$this->vendor($request);
  $d=$request->validate(['status'=>'required|in:approved,rejected,received,refunded,cancelled','resolution_note'=>'nullable|string|max:1000']);
  DB::transaction(function()use($vendor,$id,$d){
   $r=DB::table('return_requests')->where('id',$id)->where('vendor_id',$vendor->id)->lockForUpdate()->first();abort_unless($r,404);
   abort_if(in_array($r->status,['refunded','rejected','cancelled'],true)&&$d['status']!==$r->status,422,'This return is already finalized.');
   if($d['status']==='refunded'){
    abort_unless(in_array($r->status,['approved','received'],true),422,'Return must be approved or received before refund.');
    abort_if((float)$r->refund_amount<=0,422,'Refund amount is not available.');
    $wallet=DB::table('wallets')->where('user_id',$r->customer_id)->lockForUpdate()->first();abort_unless($wallet,422,'Customer wallet is not available.');
    abort_unless(strtoupper($wallet->currency)===strtoupper($r->currency),422,'Customer wallet currency does not match the refund currency.');
    $already=DB::table('wallet_transactions')->where('reference_type','return_refund')->where('reference_id',$id)->exists();
    if(!$already){$new=(float)$wallet->balance+(float)$r->refund_amount;DB::table('wallets')->where('id',$wallet->id)->update(['balance'=>$new,'updated_at'=>now()]);DB::table('wallet_transactions')->insert(['wallet_id'=>$wallet->id,'type'=>'credit','amount'=>$r->refund_amount,'balance_after'=>$new,'reference_type'=>'return_refund','reference_id'=>$id,'description'=>'Customer refund for returned order item','created_at'=>now()]);}
    DB::table('payments')->where('order_id',$r->order_id)->where('status','paid')->update(['status'=>'refunded','updated_at'=>now()]);
    DB::table('orders')->where('id',$r->order_id)->update(['payment_status'=>'refunded','updated_at'=>now()]);
   }
   DB::table('return_requests')->where('id',$id)->update(['status'=>$d['status'],'resolution_note'=>$d['resolution_note']??$r->resolution_note,'processed_at'=>in_array($d['status'],['refunded','rejected','cancelled'],true)?now():$r->processed_at,'updated_at'=>now()]);
  });
  return back()->with('success','Return request updated.');
 }

 public function wallet(Request $request){$vendor=$this->vendor($request);$wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();return view('vendor.wallet',compact('vendor','wallet'));}
 public function reviews(Request $request){$vendor=$this->vendor($request);$productIds=Product::where('vendor_id',$vendor->id)->pluck('id');return view('vendor.reviews',['vendor'=>$vendor,'reviews'=>DB::table('reviews')->whereIn('product_id',$productIds)->latest()->paginate(20)]);}
}