<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Services\PricingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller {
 public function home(){
  $featured=Product::with(['category','images','vendor'])->where('status','published')->latest()->limit(12)->get();
  $groupOffers=DB::table('group_buying_campaigns')->join('products','products.id','=','group_buying_campaigns.product_id')->where('group_buying_campaigns.status','active')->where('group_buying_campaigns.starts_at','<=',now())->where('group_buying_campaigns.ends_at','>',now())->select('group_buying_campaigns.*','products.name','products.retail_price','products.currency')->latest('group_buying_campaigns.starts_at')->limit(8)->get();
  $liveStreams=DB::table('live_streams')->join('vendors','vendors.id','=','live_streams.vendor_id')->whereIn('live_streams.status',['scheduled','live'])->select('live_streams.*','vendors.business_name')->orderBy('live_streams.starts_at')->limit(6)->get();
  $coupons=DB::table('coupons')->where('status',1)->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>=',now()))->latest('id')->limit(6)->get();
  return view('storefront.home',compact('featured','groupOffers','liveStreams','coupons'));
}
 public function shop(Request $request){$q=trim((string)$request->get('q',''));$category=$request->integer('category');$products=Product::with(['category','images','vendor'])->where('status','published')->when($q,fn($x)=>$x->where(fn($y)=>$y->where('name','like',"%{$q}%")->orWhere('description','like',"%{$q}%")))->when($category,fn($x)=>$x->where('category_id',$category))->latest()->paginate(24)->withQueryString();$categories=DB::table('categories')->where('status',1)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();return view('storefront.shop',compact('products','q','category','categories'));}
 public function search(Request $request){return $this->shop($request);}
 public function product(Request $request,int $id){$product=Product::with(['vendor','category','brand','images','videos','variants'])->where('status','published')->findOrFail($id);$customerType=$request->user()?->role==='b2b_customer'?'b2b':'b2c';$tiers=$product->pricingTiers()->where('customer_type',$customerType)->orderBy('min_quantity')->get();$price=app(PricingService::class)->unitPrice($product,1,$customerType);$reviews=DB::table('reviews')->join('users','users.id','=','reviews.user_id')->where('reviews.product_id',$product->id)->where('reviews.status','approved')->select('reviews.*','users.name as user_name')->latest('reviews.id')->limit(10)->get();$reviewCount=DB::table('reviews')->where('product_id',$product->id)->where('status','approved')->count();$avgRating=(float)(DB::table('reviews')->where('product_id',$product->id)->where('status','approved')->avg('rating')??0);$buyers=(int)DB::table('order_items')->where('product_id',$product->id)->distinct('order_id')->count('order_id');$monthlyBought=(int)DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.product_id',$product->id)->where('orders.created_at','>=',now()->subDays(30))->sum('order_items.quantity');$related=Product::with(['images','brand'])->where('status','published')->where('id','<>',$product->id)->where(fn($q)=>$q->where('category_id',$product->category_id)->orWhere('brand_id',$product->brand_id))->latest()->limit(8)->get();return view('storefront.product',compact('product','tiers','price','customerType','reviews','reviewCount','avgRating','buyers','monthlyBought','related'));}
 public function cart(Request $request){$cart=$request->session()->get('cart',[]);$products=Product::where('status','published')->whereIn('id',array_keys($cart))->get();$customerType=$request->user()?->role==='b2b_customer'?'b2b':'b2c';$pricing=app(PricingService::class);$prices=[];$total=0;foreach($products as $p){$qty=(int)$cart[$p->id];$prices[$p->id]=$pricing->unitPrice($p,$qty,$customerType);$total+=$prices[$p->id]*$qty;}return view('storefront.cart',compact('products','cart','total','prices'));}
 public function addToCart(Request $request,int $id){$p=Product::where('status','published')->findOrFail($id);$qty=max(1,(int)$request->input('quantity',1));$cart=$request->session()->get('cart',[]);$newQty=($cart[$id]??0)+$qty;abort_if($p->stock_status==='out_of_stock'||$p->stock<$newQty,422,'Requested quantity exceeds available stock.');$cart[$id]=$newQty;$request->session()->put('cart',$cart);return back()->with('success',$p->name.' added to cart.');}
 public function removeFromCart(Request $request,int $id){$cart=$request->session()->get('cart',[]);unset($cart[$id]);$request->session()->put('cart',$cart);return back()->with('success','Item removed from cart.');}
 public function checkout(Request $request){$cart=$request->session()->get('cart',[]);$products=Product::where('status','published')->whereIn('id',array_keys($cart))->get();abort_if($products->isEmpty(),404,'Cart is empty');$customerType=$request->user()->role==='b2b_customer'?'b2b':'b2c';$pricing=app(PricingService::class);$prices=[];$total=0;foreach($products as $p){$qty=(int)$cart[$p->id];$prices[$p->id]=$pricing->unitPrice($p,$qty,$customerType);$total+=$prices[$p->id]*$qty;}$methods=app(PaymentService::class)->enabledMethods();$addresses=DB::table('addresses')->where('user_id',$request->user()->id)->orderByDesc('is_default')->get();$coupons=DB::table('coupons')->where('status',1)->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>=',now()))->where(fn($q)=>$q->whereNull('usage_limit')->orWhereColumn('used_count','<','usage_limit'))->orderBy('code')->get();return view('storefront.checkout',compact('products','cart','total','methods','addresses','prices','coupons'));}
 public function placeOrder(Request $request){
  $data=$request->validate(['payment_method_id'=>'required|integer','address_id'=>'required|exists:addresses,id','coupon_code'=>'nullable|string|max:80']);
  $cart=$request->session()->get('cart',[]);
  $products=Product::where('status','published')->whereIn('id',array_keys($cart))->get()->keyBy('id');
  abort_if($products->isEmpty(),422,'Cart is empty.');
  $address=DB::table('addresses')->where('id',$data['address_id'])->where('user_id',$request->user()->id)->first();
  abort_unless($address,403);
  abort_unless(DB::table('payment_methods')->where('id',$data['payment_method_id'])->where('enabled',1)->exists(),422,'Selected payment method is unavailable.');
  $customerType=$request->user()->role==='b2b_customer'?'b2b':'b2c';
  $pricing=app(PricingService::class);
  $coupon=null;
  if(!empty($data['coupon_code'])){
   $coupon=DB::table('coupons')->where('code',strtoupper(trim($data['coupon_code'])))->where('status',1)
    ->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))
    ->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>=',now()))
    ->where(fn($q)=>$q->whereNull('usage_limit')->orWhereColumn('used_count','<','usage_limit'))->first();
   abort_unless($coupon,422,'Invalid, expired, or exhausted coupon.');
  }
  $orderIds=DB::transaction(function()use($products,$cart,$address,$data,$request,$pricing,$customerType,$coupon){
   $ids=[];$appliedCoupon=false;$vendorGroups=$products->groupBy('vendor_id');
   $groupSubtotals=[];
   foreach($vendorGroups as $vendorId=>$items){
    $subtotal=0;
    foreach($items as $p){$qty=(int)$cart[$p->id];abort_if((float)$p->stock<$qty||$p->stock_status==='out_of_stock',422,'Stock changed. Please review your cart.');$subtotal+=$pricing->unitPrice($p,$qty,$customerType)*$qty;}
    $groupSubtotals[(int)$vendorId]=$subtotal;
   }
   $totalCartSubtotal=array_sum($groupSubtotals);
   $globalDiscount=0;$globalRemaining=0;
   if($coupon && is_null($coupon->vendor_id) && ($coupon->min_order===null || $totalCartSubtotal >= (float)$coupon->min_order)){
    $globalDiscount=$coupon->type==='percent' ? $totalCartSubtotal*((float)$coupon->value/100) : (float)$coupon->value;
    if($coupon->max_discount!==null)$globalDiscount=min($globalDiscount,(float)$coupon->max_discount);
    $globalDiscount=min($globalDiscount,$totalCartSubtotal);$globalRemaining=$globalDiscount;
   }
   foreach($vendorGroups as $vendorId=>$items){
    $subtotal=$groupSubtotals[(int)$vendorId];$discount=0;
    if($coupon && is_null($coupon->vendor_id) && $globalDiscount>0){
     $discount=$totalCartSubtotal>0 ? $globalDiscount*($subtotal/$totalCartSubtotal) : 0;
     $globalRemaining=max(0,$globalRemaining-$discount);
    }elseif($coupon && (int)$coupon->vendor_id===(int)$vendorId && ($coupon->min_order===null || $subtotal >= (float)$coupon->min_order)){
     $discount=$coupon->type==='percent' ? $subtotal*((float)$coupon->value/100) : (float)$coupon->value;
     if($coupon->max_discount!==null)$discount=min($discount,(float)$coupon->max_discount);
     $discount=min($discount,$subtotal);
    }
    $discount=round($discount,2);
    if($discount>0)$appliedCoupon=true;
    $order=new Order;$order->user_id=$request->user()->id;$order->order_number='CP-'.strtoupper(bin2hex(random_bytes(5)));$order->status='pending';$order->payment_status='pending';$order->fulfillment_status='unfulfilled';$order->currency='USD';$order->subtotal=$subtotal;$order->discount_total=$discount;$order->grand_total=max(0,$subtotal-$discount);$order->shipping_address=(array)$address;$order->billing_address=(array)$address;$order->save();
    foreach($items as $p){$qty=(int)$cart[$p->id];$unit=$pricing->unitPrice($p,$qty,$customerType);DB::table('order_items')->insert(['order_id'=>$order->id,'vendor_id'=>$p->vendor_id,'product_id'=>$p->id,'product_name'=>$p->name,'sku'=>$p->sku,'quantity'=>$qty,'unit_price'=>$unit,'subtotal'=>$unit*$qty]);DB::table('products')->where('id',$p->id)->decrement('stock',$qty);DB::table('products')->where('id',$p->id)->update(['stock_status'=>DB::raw("CASE WHEN stock <= 0 THEN 'out_of_stock' ELSE 'in_stock' END")]);}
    app(PaymentService::class)->createPayment($order,(int)$data['payment_method_id']);$ids[]=$order->id;
   }
   if($coupon && $appliedCoupon)DB::table('coupons')->where('id',$coupon->id)->increment('used_count');
   return $ids;
  });
  $request->session()->forget('cart');
  return redirect()->route('customer.orders')->with('success','Order(s) placed successfully: #'.implode(', #',$orderIds));
 }

 public function customerDashboard(Request $request){$user=$request->user();$stats=['orders'=>DB::table('orders')->where('user_id',$user->id)->count(),'pending'=>DB::table('orders')->where('user_id',$user->id)->whereIn('status',['pending','processing'])->count(),'wishlist'=>DB::table('wishlists')->where('user_id',$user->id)->count(),'notifications'=>DB::table('notifications')->where('user_id',$user->id)->whereNull('read_at')->count()];return view('customer.dashboard',compact('user','stats'));}
 public function customerOrders(Request $request){$orders=DB::table('orders')->where('user_id',$request->user()->id)->latest()->paginate(20);return view('customer.orders',compact('orders'));}
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