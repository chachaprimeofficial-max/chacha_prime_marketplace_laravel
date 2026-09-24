<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Services\PricingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller {
 public function home(){
  $countryId=(int)(session('marketplace_country_id') ?: DB::table('countries')->where('code','PK')->value('id') ?: 0);
  $categories=DB::table('categories')->where('status',1)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->limit(12)->get();
  $featured=Product::with(['category','images','vendor'])->where('status','published')->when($countryId,fn($q)=>$q->where(fn($x)=>$x->where('country_id',$countryId)->orWhereHas('marketplaces',fn($m)=>$m->where('countries.id',$countryId)->where('product_marketplaces.active',1))))->latest()->limit(12)->get();
  $groupOffers=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('group_buying_campaigns.status','active')->where('group_buying_campaigns.starts_at','<=',now())->where('group_buying_campaigns.ends_at','>',now())->select('group_buying_campaigns.*','products.name','products.retail_price','products.currency')->latest('group_buying_campaigns.starts_at')->limit(8)->get();
  $liveStreams=DB::table('live_streams')->join('vendors','vendors.id','=','live_streams.vendor_id')->whereIn('live_streams.status',['scheduled','live'])->select('live_streams.*','vendors.business_name')->orderBy('live_streams.starts_at')->limit(6)->get();
  $coupons=DB::table('coupons')->where('status',1)->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>=',now()))->latest('id')->limit(6)->get();
  return view('storefront.home',compact('categories','featured','groupOffers','liveStreams','coupons'));
}
 public function shop(Request $request){$countryId=(int)session('marketplace_country_id',0);$q=trim((string)$request->get('q',''));$category=$request->integer('category');$minPrice=$request->input('min_price');$maxPrice=$request->input('max_price');$inStock=$request->boolean('in_stock');$sort=$request->get('sort','relevance');$products=Product::with(['category','images','vendor'])->where('status','published')->when($countryId,fn($x)=>$x->where(fn($z)=>$z->where('country_id',$countryId)->orWhereHas('marketplaces',fn($m)=>$m->where('countries.id',$countryId)->where('product_marketplaces.active',1))))->when($q,fn($x)=>$x->where(fn($y)=>$y->where('name','like',"%{$q}%")->orWhere('description','like',"%{$q}%")->orWhere('sku','like',"%{$q}%")))->when($category,fn($x)=>$x->where('category_id',$category))->when($minPrice!==null&&$minPrice!=='',fn($x)=>$x->where('retail_price','>=',(float)$minPrice))->when($maxPrice!==null&&$maxPrice!=='',fn($x)=>$x->where('retail_price','<=',(float)$maxPrice))->when($inStock,fn($x)=>$x->where('stock','>',0)->where('stock_status','<>','out_of_stock'))->when($sort==='price_asc',fn($x)=>$x->orderBy('retail_price'))->when($sort==='price_desc',fn($x)=>$x->orderByDesc('retail_price'))->when($sort==='newest',fn($x)=>$x->latest())->when($sort==='relevance',fn($x)=>$x->latest())->paginate(24)->withQueryString();$categories=DB::table('categories')->where('status',1)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();return view('storefront.shop',compact('products','q','category','categories','minPrice','maxPrice','inStock','sort','countryId'));}
 public function search(Request $request){return $this->shop($request);}
 public function product(Request $request,int $id){$countryId=(int)session('marketplace_country_id',0);$product=Product::with(['vendor','category','brand','images','videos','variants'])->where('status','published')->when($countryId,fn($x)=>$x->where(fn($z)=>$z->where('country_id',$countryId)->orWhereHas('marketplaces',fn($m)=>$m->where('countries.id',$countryId)->where('product_marketplaces.active',1))))->findOrFail($id);$customerType=$request->user()?->role==='b2b_customer'?'b2b':'b2c';$tiers=$product->pricingTiers()->where('customer_type',$customerType)->orderBy('min_quantity')->get();$price=app(PricingService::class)->unitPrice($product,1,$customerType);$reviews=DB::table('reviews')->join('users','users.id','=','reviews.user_id')->where('reviews.product_id',$product->id)->where('reviews.status','approved')->select('reviews.*','users.name as user_name')->latest('reviews.id')->limit(10)->get();$reviewCount=DB::table('reviews')->where('product_id',$product->id)->where('status','approved')->count();$avgRating=(float)(DB::table('reviews')->where('product_id',$product->id)->where('status','approved')->avg('rating')??0);$buyers=(int)DB::table('order_items')->where('product_id',$product->id)->distinct('order_id')->count('order_id');$monthlyBought=(int)DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.product_id',$product->id)->where('orders.created_at','>=',now()->subDays(30))->sum('order_items.quantity');$brandProducts=Product::with(['images','brand'])->where('status','published')->where('id','<>',$product->id)->when($product->brand_id,fn($q)=>$q->where('brand_id',$product->brand_id))->latest()->limit(4)->get();$relatedProducts=Product::with(['images','brand'])->where('status','published')->where('id','<>',$product->id)->when($product->category_id,fn($q)=>$q->where('category_id',$product->category_id))->latest()->limit(4)->get();return view('storefront.product',compact('product','tiers','price','customerType','reviews','reviewCount','avgRating','buyers','monthlyBought','brandProducts','relatedProducts'));}
 public function cart(Request $request){$cart=$request->session()->get('cart',[]);$products=Product::where('status','published')->whereIn('id',array_keys($cart))->get();$customerType=$request->user()?->role==='b2b_customer'?'b2b':'b2c';$pricing=app(PricingService::class);$prices=[];$total=0;foreach($products as $p){$qty=(int)$cart[$p->id];$prices[$p->id]=$pricing->unitPrice($p,$qty,$customerType);$total+=$prices[$p->id]*$qty;}return view('storefront.cart',compact('products','cart','total','prices'));}
 public function addToCart(Request $request,int $id){$p=Product::where('status','published')->findOrFail($id);$qty=max(1,(int)$request->input('quantity',1));$cart=$request->session()->get('cart',[]);$newQty=($cart[$id]??0)+$qty;abort_if($p->stock_status==='out_of_stock'||$p->stock<$newQty,422,'Requested quantity exceeds available stock.');$cart[$id]=$newQty;$request->session()->put('cart',$cart);return back()->with('success',$p->name.' added to cart.');}
 public function removeFromCart(Request $request,int $id){$cart=$request->session()->get('cart',[]);unset($cart[$id]);$request->session()->put('cart',$cart);return back()->with('success','Item removed from cart.');}
 public function checkout(Request $request){$cart=$request->session()->get('cart',[]);$products=Product::where('status','published')->whereIn('id',array_keys($cart))->get();abort_if($products->isEmpty(),404,'Cart is empty');$customerType=$request->user()->role==='b2b_customer'?'b2b':'b2c';$pricing=app(PricingService::class);$prices=[];$total=0;foreach($products as $p){$qty=(int)$cart[$p->id];$prices[$p->id]=$pricing->unitPrice($p,$qty,$customerType);$total+=$prices[$p->id]*$qty;}$methods=app(PaymentService::class)->enabledMethods();$addresses=DB::table('addresses')->where('user_id',$request->user()->id)->orderByDesc('is_default')->get();$coupons=DB::table('coupons')->where('status',1)->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>=',now()))->where(fn($q)=>$q->whereNull('usage_limit')->orWhereColumn('used_count','<','usage_limit'))->orderBy('code')->get();return view('storefront.checkout',compact('products','cart','total','methods','addresses','prices','coupons'));}
 public function placeOrder(Request $request){
  $data=$request->validate(['payment_method_id'=>'required|integer','address_id'=>'required|exists:addresses,id','coupon_code'=>'nullable|string|max:80']);
  $cart=$request->session()->get('cart',[]);
  abort_if(empty($cart),422,'Cart is empty.');
  $address=DB::table('addresses')->where('id',$data['address_id'])->where('user_id',$request->user()->id)->first();
  abort_unless($address,403,'Selected address is not available.');
  abort_unless(DB::table('payment_methods')->where('id',$data['payment_method_id'])->where('enabled',1)->exists(),422,'Selected payment method is unavailable.');
  $customerType=$request->user()->role==='b2b_customer'?'b2b':'b2c';
  $pricing=app(PricingService::class);
  $couponCode=trim((string)($data['coupon_code']??''));
  $orderIds=DB::transaction(function()use($cart,$address,$data,$request,$pricing,$customerType,$couponCode){
   $productIds=array_map('intval',array_keys($cart));
   $countryId=(int)($request->session()->get('marketplace_country_id') ?: DB::table('countries')->where('code','PK')->value('id'));
   $products=Product::where('status','published')->whereIn('id',$productIds)->where(fn($q)=>$q->where('country_id',$countryId)->orWhereHas('marketplaces',fn($m)=>$m->where('countries.id',$countryId)->where('product_marketplaces.active',1)))->lockForUpdate()->get()->keyBy('id');
   abort_if($products->count()!==count($productIds),422,'One or more cart items are no longer available.');
   $currencies=$products->pluck('currency')->map(fn($v)=>strtoupper((string)$v))->unique()->values();
   abort_if($currencies->count()>1,422,'Your cart contains multiple currencies. Please checkout items with the same currency separately.');
   $currency=$currencies->first() ?: 'USD';
   $coupon=null;
   if($couponCode!==''){
    $coupon=DB::table('coupons')->where('code',strtoupper($couponCode))->where('status',1)->lockForUpdate()->first();
    abort_unless($coupon,422,'Invalid coupon.');
    abort_if($coupon->starts_at && now()->lt($coupon->starts_at),422,'Coupon is not active yet.');
    abort_if($coupon->ends_at && now()->gt($coupon->ends_at),422,'Coupon has expired.');
    abort_if($coupon->usage_limit!==null && $coupon->used_count >= $coupon->usage_limit,422,'Coupon usage limit has been reached.');
    abort_if($coupon->vendor_id && !$products->contains(fn($p)=>(int)$p->vendor_id===(int)$coupon->vendor_id),422,'This coupon does not apply to your cart.');
   }
   $vendorGroups=$products->groupBy('vendor_id'); $ids=[]; $couponUsed=false; $cartSubtotal=0;
   foreach($vendorGroups as $items){
    foreach($items as $p){$qty=max(1,(int)$cart[$p->id]);abort_if($p->stock_status==='out_of_stock'||(float)$p->stock<$qty,422,'Stock changed. Please review your cart.');$cartSubtotal+=round($pricing->unitPrice($p,$qty,$customerType)*$qty,2);}
   }
   $globalDiscount=0;
   if($coupon && !$coupon->vendor_id && (!$coupon->min_order || $cartSubtotal >= (float)$coupon->min_order)){
    $globalDiscount=$coupon->type==='percent' ? $cartSubtotal*((float)$coupon->value/100) : (float)$coupon->value;
    if($coupon->max_discount!==null)$globalDiscount=min($globalDiscount,(float)$coupon->max_discount);
    $globalDiscount=min($globalDiscount,$cartSubtotal);
   }
   foreach($vendorGroups as $vendorId=>$items){
    $subtotal=0; foreach($items as $p){$qty=max(1,(int)$cart[$p->id]);$subtotal+=round($pricing->unitPrice($p,$qty,$customerType)*$qty,2);}
    $discount=0;
    if($coupon && !$coupon->vendor_id && $globalDiscount>0)$discount=round($globalDiscount*($subtotal/max($cartSubtotal,1)),2);
    elseif($coupon && (int)$coupon->vendor_id===(int)$vendorId && (!$coupon->min_order || $subtotal >= (float)$coupon->min_order)){$discount=$coupon->type==='percent'?$subtotal*((float)$coupon->value/100):(float)$coupon->value;if($coupon->max_discount!==null)$discount=min($discount,(float)$coupon->max_discount);$discount=min($discount,$subtotal);}
    $discount=round(min($discount,$subtotal),2);
    $order=new Order;
    $order->user_id=$request->user()->id;$order->country_id=$countryId;$order->order_number='CP-'.strtoupper(bin2hex(random_bytes(5)));$order->status='pending';$order->payment_status='pending';$order->fulfillment_status='unfulfilled';$order->currency=$currency;$order->subtotal=round($subtotal,2);$order->discount_total=$discount;$order->shipping_total=0;$order->tax_total=0;$order->grand_total=round(max(0,$subtotal-$discount),2);$order->shipping_address=(array)$address;$order->billing_address=(array)$address;$order->save();
    foreach($items as $p){$qty=max(1,(int)$cart[$p->id]);$unit=round($pricing->unitPrice($p,$qty,$customerType),2);DB::table('order_items')->insert(['order_id'=>$order->id,'vendor_id'=>$p->vendor_id,'product_id'=>$p->id,'product_name'=>$p->name,'sku'=>$p->sku,'quantity'=>$qty,'unit_price'=>$unit,'subtotal'=>round($unit*$qty,2),'vendor_status'=>'pending']);$newStock=(float)$p->stock-$qty;DB::table('products')->where('id',$p->id)->update(['stock'=>$newStock,'stock_status'=>$newStock<=0?'out_of_stock':'in_stock']);}
    app(PaymentService::class)->createPayment($order,(int)$data['payment_method_id']);$ids[]=$order->id;
    if($discount>0)$couponUsed=true;
   }
   if($coupon && $couponUsed)DB::table('coupons')->where('id',$coupon->id)->increment('used_count');
   return $ids;
  });
  $request->session()->forget('cart');
  return redirect()->route('customer.orders')->with('success','Order(s) placed successfully: #'.implode(', #',$orderIds));
 }
 public function returns(Request $request){
  $returns=DB::table('return_requests')->join('orders','orders.id','=','return_requests.order_id')->join('vendors','vendors.id','=','return_requests.vendor_id')->where('return_requests.customer_id',$request->user()->id)->select('return_requests.*','orders.order_number','vendors.business_name')->latest('return_requests.id')->paginate(20);
  return view('customer.returns',compact('returns'));
 }
 public function requestReturn(Request $request,int $orderId){
  $d=$request->validate([
   'order_item_id'=>'required|integer',
   'reason'=>'required|string|max:190',
   'details'=>'nullable|string|max:2000',
   'evidence_images'=>'required|array|min:1|max:8',
   'evidence_images.*'=>'image|mimes:jpg,jpeg,png,webp|max:10240',
   'evidence_videos'=>'required|array|min:1|max:4',
   'evidence_videos.*'=>'file|mimes:mp4,webm,mov|max:102400',
  ]);
  $imageFiles=$request->file('evidence_images',[]);
  $videoFiles=$request->file('evidence_videos',[]);
  abort_unless(count($imageFiles)>=1 && count($videoFiles)>=1,422,'At least one photo and one video are required for a return request.');
  DB::transaction(function()use($request,$orderId,$d,$imageFiles,$videoFiles){
   $order=DB::table('orders')->where('id',$orderId)->where('user_id',$request->user()->id)->lockForUpdate()->first();abort_unless($order,404);
   abort_unless($order->fulfillment_status==='delivered',422,'A return can be requested after delivery.');
   $shipment=DB::table('shipments')->where('order_id',$orderId)->where('status','delivered')->whereNotNull('delivered_at')->orderByDesc('delivered_at')->first();
   abort_unless($shipment,422,'The delivery date could not be verified.');
   $deliveredAt=\Illuminate\Support\Carbon::parse($shipment->delivered_at);
   $deadline=$deliveredAt->copy()->addDays(7);
   abort_if(now()->gt($deadline),422,'The 7-day return period has expired. Returns are not accepted after the deadline.');
   $item=DB::table('order_items')->where('id',$d['order_item_id'])->where('order_id',$orderId)->first();abort_unless($item,404);
   abort_if(DB::table('return_requests')->where('order_item_id',$item->id)->whereIn('status',['requested','approved','received'])->exists(),422,'A return request already exists for this item.');
   $images=[];$videos=[];
   foreach($imageFiles as $file){$images[]=$file->store('returns/evidence/images','public');}
   foreach($videoFiles as $file){$videos[]=$file->store('returns/evidence/videos','public');}
   $amount=round((float)$item->unit_price*(float)$item->quantity,2);
   DB::table('return_requests')->insert([
    'order_id'=>$orderId,'order_item_id'=>$item->id,'vendor_id'=>$item->vendor_id,'customer_id'=>$request->user()->id,
    'reason'=>$d['reason'],'details'=>$d['details']??null,'evidence_images'=>json_encode($images),'evidence_videos'=>json_encode($videos),
    'delivered_at'=>$deliveredAt,'return_deadline_at'=>$deadline,'refund_amount'=>$amount,'currency'=>$order->currency,'status'=>'requested',
    'created_at'=>now(),'updated_at'=>now()
   ]);
  });
  return back()->with('success','Return request submitted with photo and video evidence. The seller can now review it.');
 }

 public function customerDashboard(Request $request){$user=$request->user();$stats=['orders'=>DB::table('orders')->where('user_id',$user->id)->count(),'pending'=>DB::table('orders')->where('user_id',$user->id)->whereIn('status',['pending','processing'])->count(),'wishlist'=>DB::table('wishlists')->where('user_id',$user->id)->count(),'notifications'=>DB::table('notifications')->where('user_id',$user->id)->whereNull('read_at')->count()];return view('customer.dashboard',compact('user','stats'));}
 public function customerOrders(Request $request){$orders=DB::table('orders')->where('user_id',$request->user()->id)->latest()->paginate(20);return view('customer.orders',compact('orders'));}
 public function invoice(Request $request,int $id){$order=DB::table('orders')->where('id',$id)->where('user_id',$request->user()->id)->firstOrFail();$items=DB::table('order_items')->where('order_id',$id)->get();$identifier=DB::table('product_identifiers')->whereIn('product_id',$items->pluck('product_id'))->get()->keyBy('product_id');return view('customer.invoice',compact('order','items','identifier'));}
 public function orderDetail(Request $request,int $id){$order=DB::table('orders')->where('id',$id)->where('user_id',$request->user()->id)->firstOrFail();$items=DB::table('order_items')->where('order_id',$id)->get();$shipments=DB::table('shipments')->where('order_id',$id)->latest()->get();return view('customer.order-detail',compact('order','items','shipments'));}
 public function wishlist(Request $request){$items=DB::table('wishlists')->join('products','products.id','=','wishlists.product_id')->where('wishlists.user_id',$request->user()->id)->select('wishlists.*','products.name','products.retail_price','products.currency')->latest('wishlists.created_at')->get();return view('customer.wishlist',compact('items'));}
 public function addWishlist(Request $request,int $id){Product::where('status','published')->findOrFail($id);DB::table('wishlists')->updateOrInsert(['user_id'=>$request->user()->id,'product_id'=>$id],['created_at'=>now()]);return back()->with('success','Added to wishlist.');}
 public function removeWishlist(Request $request,int $id){DB::table('wishlists')->where('user_id',$request->user()->id)->where('product_id',$id)->delete();return back()->with('success','Removed from wishlist.');}
 public function addresses(Request $request){$addresses=DB::table('addresses')->where('user_id',$request->user()->id)->orderByDesc('is_default')->get();return view('customer.addresses',compact('addresses'));}
 public function storeAddress(Request $request){$data=$request->validate(['label'=>'nullable|string|max:60','recipient_name'=>'required|string|max:120','phone'=>'nullable|string|max:40','country'=>'required|string|max:100','state'=>'nullable|string|max:120','city'=>'nullable|string|max:120','postal_code'=>'nullable|string|max:30','address_line1'=>'required|string|max:255','address_line2'=>'nullable|string|max:255','is_default'=>'nullable|boolean']);if(!empty($data['is_default']))DB::table('addresses')->where('user_id',$request->user()->id)->update(['is_default'=>0]);$data['user_id']=$request->user()->id;$data['is_default']=(int)($data['is_default']??0);$data['created_at']=now();$data['updated_at']=now();DB::table('addresses')->insert($data);return back()->with('success','Address saved.');}
 public function wallet(Request $request){$wallet=DB::table('wallets')->where('user_id',$request->user()->id)->first();$transactions=$wallet?DB::table('wallet_transactions')->where('wallet_id',$wallet->id)->latest()->limit(30)->get():collect();return view('customer.wallet',compact('wallet','transactions'));}
 public function card(Request $request){$card=DB::table('virtual_cards')->where('user_id',$request->user()->id)->first();return view('customer.card',compact('card'));}
 public function groupBuying(){ $campaigns=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('group_buying_campaigns.status','active')->where('group_buying_campaigns.starts_at','<=',now())->where('group_buying_campaigns.ends_at','>',now())->select('group_buying_campaigns.*','products.name','products.retail_price','products.currency')->latest('group_buying_campaigns.starts_at')->paginate(20);return view('storefront.group-buying',compact('campaigns'));}
 public function joinGroup(Request $request,int $id){$campaign=DB::table('group_buying_campaigns')->where('id',$id)->where('status','active')->where('starts_at','<=',now())->where('ends_at','>',now())->firstOrFail();$count=DB::table('group_buying_participants')->where('campaign_id',$id)->whereIn('status',['reserved','confirmed'])->sum('quantity');abort_if($count >= $campaign->target_participants,422,'This group is already full.');abort_if(DB::table('group_buying_participants')->where('campaign_id',$id)->where('user_id',$request->user()->id)->exists(),422,'You already joined this group.');DB::table('group_buying_participants')->insert(['campaign_id'=>$id,'user_id'=>$request->user()->id,'quantity'=>1,'status'=>'reserved','joined_at'=>now()]);return back()->with('success','You joined the group buying campaign.');}
 public function liveShopping(){ $streams=DB::table('live_streams')->join('vendors','vendors.id','=','live_streams.vendor_id')->whereIn('live_streams.status',['scheduled','live'])->select('live_streams.*','vendors.business_name')->orderBy('live_streams.starts_at')->paginate(20);foreach($streams as $s){$s->youtube_video_id=$s->youtube_video_id ?: $this->youtubeId($s->youtube_url);$s->pinned_products=DB::table('live_products')->join('products','products.id','=','live_products.product_id')->where('live_products.live_stream_id',$s->id)->where('products.status','published')->select('products.id','products.name','products.retail_price','products.currency')->orderBy('live_products.pin_order')->get();}return view('storefront.live-shopping',compact('streams'));}
 private function youtubeId(string $url): ?string {$parts=parse_url($url);$q=[];if(!empty($parts['query']))parse_str($parts['query'],$q);if(!empty($q['v']))return preg_replace('/[^A-Za-z0-9_-]/','',$q['v']);$path=$parts['path']??'';if(preg_match('~/(?:live/|embed/|shorts/)?([A-Za-z0-9_-]{6,})~',$path,$m))return $m[1];return null;}
 public function notifications(Request $request){$notifications=DB::table('notifications')->where('user_id',$request->user()->id)->latest()->paginate(30);DB::table('notifications')->where('user_id',$request->user()->id)->whereNull('read_at')->update(['read_at'=>now()]);return view('customer.notifications',compact('notifications'));}
}