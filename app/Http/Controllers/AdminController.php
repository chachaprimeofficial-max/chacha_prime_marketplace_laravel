<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AuditLogService;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard',[
            'stats'=>[
                'users'=>User::count(),'customers'=>User::whereIn('role',['customer','b2b_customer'])->count(),
                'vendors'=>Vendor::count(),'pendingVendors'=>Vendor::where('status','pending')->count(),
                'products'=>Product::count(),'pendingProducts'=>Product::where('status','pending')->count(),
                'orders'=>DB::table('orders')->count(),'sales'=>DB::table('orders')->where('payment_status','paid')->sum('grand_total'),
                'payments'=>DB::table('payments')->count(),'pendingPayments'=>DB::table('payments')->where('status','pending')->count(),
                'wallets'=>DB::table('wallets')->count(),'cards'=>DB::table('virtual_cards')->count(),
                'reviews'=>DB::table('reviews')->count(),'pendingReviews'=>DB::table('reviews')->where('status','pending')->count(),
                'groupCampaigns'=>DB::table('group_buying_campaigns')->count(),'liveStreams'=>DB::table('live_streams')->count(),
            ],
            'recentOrders'=>DB::table('orders')->latest('id')->limit(8)->get(),
            'recentUsers'=>User::latest()->limit(8)->get(),
        ]);
    }
    public function catalogTools(){
        $imports=DB::table('catalog_imports')->latest('id')->paginate(15);
        return view('admin.catalog-tools',['imports'=>$imports,'identifierCount'=>DB::table('product_identifiers')->count(),'subscriptionPlans'=>DB::table('subscription_plans')->where('status',1)->count()]);
    }
    public function storeSubscriptionPlan(Request $request){
        $data=$request->validate(['name'=>'required|string|max:120','audience'=>'required|in:vendor,b2b_customer,customer','price'=>'required|numeric|min:0','currency'=>'required|string|size:3','billing_cycle'=>'required|in:monthly,yearly','product_limit'=>'nullable|integer|min:0','ai_limit'=>'nullable|integer|min:0','import_limit'=>'nullable|integer|min:0']);
        $data['created_at']=now();$data['updated_at']=now();DB::table('subscription_plans')->insert($data);return back()->with('success','Subscription plan created.');
    }
    public function scanCenter(){return view('admin.scan-center');}
    public function scanLookup(Request $request){
        $q=trim((string)$request->input('q','')); abort_if($q==='','404');
        $product=DB::table('product_identifiers')->join('products','products.id','=','product_identifiers.product_id')->leftJoin('vendors','vendors.id','=','products.vendor_id')->where(function($w)use($q){$w->where('product_identifiers.product_code',$q)->orWhere('product_identifiers.internal_sku',$q)->orWhere('product_identifiers.barcode_value',$q)->orWhere('product_identifiers.qr_token',$q)->orWhere('products.sku',$q); })->select('products.id','products.name','products.sku','products.status','products.stock','product_identifiers.*','vendors.business_name')->first();
        $order=DB::table('orders')->where('order_number',$q)->first();
        return view('admin.scan-center',['query'=>$q,'product'=>$product,'order'=>$order]);
    }
    public function ensureProductIdentifier(int $id){
        $p=DB::table('products')->where('id',$id)->first();abort_unless($p,404);
        $existing=DB::table('product_identifiers')->where('product_id',$id)->first();if(!$existing){$code='CP-PROD-'.str_pad((string)$id,8,'0',STR_PAD_LEFT);$sku=$p->sku ?: 'CP-'.strtoupper(substr(hash('sha256',$p->name.$id),0,10));$barcode='CP'.str_pad((string)$id,12,'0',STR_PAD_LEFT);$token=bin2hex(random_bytes(20));DB::table('product_identifiers')->insert(['product_id'=>$id,'product_code'=>$code,'internal_sku'=>$sku,'barcode_value'=>$barcode,'barcode_type'=>'CODE128','qr_token'=>$token,'created_at'=>now(),'updated_at'=>now()]);}
        return back()->with('success','Product identifier generated.');
    }
    public function users(){return view('admin.users',['users'=>User::latest()->paginate(20)]);}
    public function vendors(){return view('admin.vendors',['vendors'=>Vendor::with('user')->latest()->paginate(20)]);}
    public function products(){return view('admin.products',['products'=>Product::with(['vendor','category'])->latest()->paginate(20)]);}
    public function categories(){return view('admin.categories',['categories'=>Category::latest()->paginate(20)]);}

    public function updateUser(Request $request,User $user){
        $data=$request->validate(['status'=>'required|in:active,pending,blocked,suspended','role'=>'required|in:super_admin,admin,vendor,b2b_customer,customer,affiliate,courier,streamer']);
        $before=$user->only(['status','role']); $user->update($data); app(AuditLogService::class)->log('user.updated','User',$user->id,['before'=>$before,'after'=>$data]); return back()->with('success','User updated.');
    }
    public function updateVendor(Request $request,Vendor $vendor){
        $data=$request->validate(['status'=>'required|in:pending,approved,suspended,rejected','verification_status'=>'required|in:pending,verified,rejected']);
        $before=$vendor->only(['status','verification_status']); $vendor->update($data); app(AuditLogService::class)->log('vendor.updated','Vendor',$vendor->id,['before'=>$before,'after'=>$data]); if($data['status']==='approved') $vendor->user?->update(['status'=>'active','role'=>'vendor']); return back()->with('success','Vendor updated.');
    }
    public function storeProduct(Request $request){
        $data=$request->validate(['vendor_id'=>'required|exists:vendors,id','category_id'=>'nullable|exists:categories,id','brand_id'=>'nullable|exists:brands,id','name'=>'required|string|max:220','retail_price'=>'required|numeric|min:0','cost_price'=>'nullable|numeric|min:0','currency'=>'required|string|size:3','stock'=>'required|numeric|min:0','description'=>'nullable|string']);
        $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(6)); $data['sku']='CP-'.strtoupper(Str::random(8)); $data['status']='pending'; $data['stock_status']=$data['stock']>0?'in_stock':'out_of_stock'; $product=Product::create($data); app(AuditLogService::class)->log('product.created','Product',$product->id,['name'=>$product->name,'vendor_id'=>$product->vendor_id,'status'=>$product->status]); return back()->with('success','Product created.');
    }
    public function storeCategory(Request $request){
        $data=$request->validate(['name'=>'required|string|max:150','parent_id'=>'nullable|exists:categories,id','description'=>'nullable|string']);
        $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5)); $data['status']=1; $category=Category::create($data); app(AuditLogService::class)->log('category.created','Category',$category->id,['name'=>$category->name,'parent_id'=>$category->parent_id]); return back()->with('success','Category created.');
    }

    public function module($module){
        $allowed=['brands','reviews','coupons','shipping','currencies','payment-methods','settings','pages','group-buying','live-commerce','ai-logs','audit-logs'];
        abort_unless(in_array($module,$allowed,true),404);
        $map=[
            'brands'=>['table'=>'brands','title'=>'Brands'],'reviews'=>['table'=>'reviews','title'=>'Reviews'],'coupons'=>['table'=>'coupons','title'=>'Coupons'],
            'shipping'=>['table'=>'shipping_methods','title'=>'Shipping Methods'],'currencies'=>['table'=>'currencies','title'=>'Currencies'],
            'payment-methods'=>['table'=>'payment_methods','title'=>'Payment Methods'],'settings'=>['table'=>'settings','title'=>'Settings'],
            'pages'=>['table'=>'pages','title'=>'CMS Pages'],'group-buying'=>['table'=>'group_buying_campaigns','title'=>'Group Buying'],
            'live-commerce'=>['table'=>'live_streams','title'=>'Live Commerce'],'ai-logs'=>['table'=>'ai_logs','title'=>'AI Activity'],
            'audit-logs'=>['table'=>'audit_logs','title'=>'Audit Logs'],
        ];
        $m=$map[$module]; return view('admin.module',['module'=>$module,'title'=>$m['title'],'rows'=>DB::table($m['table'])->latest('id')->paginate(30)]);
    }
    public function storeModule(Request $request,string $module){
        $schemas=[
            'brands'=>['table'=>'brands','fields'=>['name'=>'required|string|max:150','logo'=>'nullable|string|max:500']],
            'coupons'=>['table'=>'coupons','fields'=>['code'=>'required|string|max:80','type'=>'required|string|max:20','value'=>'required|numeric|min:0','min_order'=>'nullable|numeric|min:0','max_discount'=>'nullable|numeric|min:0']],
            'currencies'=>['table'=>'currencies','fields'=>['code'=>'required|string|size:3','name'=>'required|string|max:80','symbol'=>'nullable|string|max:10','rate_to_base'=>'required|numeric|min:0']],
            'payment-methods'=>['table'=>'payment_methods','fields'=>['code'=>'required|string|max:80','name'=>'required|string|max:120','type'=>'required|string|max:30']],
            'shipping'=>['table'=>'shipping_methods','fields'=>['code'=>'required|string|max:80','name'=>'required|string|max:120','provider'=>'nullable|string|max:100','mode'=>'required|string|max:20']],
            'pages'=>['table'=>'pages','fields'=>['slug'=>'required|string|max:180','title'=>'required|string|max:220','content'=>'nullable|string']],
        ];
        abort_unless(isset($schemas[$module]),404); $schema=$schemas[$module];
        $data=$request->validate($schema['fields']); $data['created_at']=now(); $data['updated_at']=now();
        if(in_array($module,['brands','coupons','currencies','payment-methods','shipping'],true)) $data['enabled']=1;
        if($module==='pages') $data['status']=1;
        $id=DB::table($schema['table'])->insertGetId($data); app(AuditLogService::class)->log('module.created',$schema['table'],$id,['module'=>$module,'data'=>$data]); return back()->with('success',$schema['table'].' record created.');
    }

    public function updateModule(Request $request,string $module,int $id){
        $schemas=[
            'brands'=>['table'=>'brands','fields'=>['name'=>'required|string|max:150','logo'=>'nullable|string|max:500']],
            'coupons'=>['table'=>'coupons','fields'=>['code'=>'required|string|max:80','type'=>'required|string|max:20','value'=>'required|numeric|min:0','min_order'=>'nullable|numeric|min:0','max_discount'=>'nullable|numeric|min:0']],
            'currencies'=>['table'=>'currencies','fields'=>['name'=>'required|string|max:80','symbol'=>'nullable|string|max:10','rate_to_base'=>'required|numeric|min:0']],
            'payment-methods'=>['table'=>'payment_methods','fields'=>['name'=>'required|string|max:120','type'=>'required|string|max:30']],
            'shipping'=>['table'=>'shipping_methods','fields'=>['name'=>'required|string|max:120','provider'=>'nullable|string|max:100','mode'=>'required|string|max:20']],
            'pages'=>['table'=>'pages','fields'=>['slug'=>'required|string|max:180','title'=>'required|string|max:220','content'=>'nullable|string']],
        ];
        abort_unless(isset($schemas[$module]),404); $schema=$schemas[$module]; $data=$request->validate($schema['fields']); $data['updated_at']=now();
        $before=DB::table($schema['table'])->where('id',$id)->first(); DB::table($schema['table'])->where('id',$id)->update($data); app(AuditLogService::class)->log('module.updated',$schema['table'],$id,['module'=>$module,'before'=>$before ? (array)$before : [],'after'=>$data]); return back()->with('success','Record updated.');
    }
    public function deleteModule(string $module,int $id){
        $tables=['brands'=>'brands','coupons'=>'coupons','currencies'=>'currencies','payment-methods'=>'payment_methods','shipping'=>'shipping_methods','pages'=>'pages'];
        abort_unless(isset($tables[$module]),404); $before=DB::table($tables[$module])->where('id',$id)->first(); DB::table($tables[$module])->where('id',$id)->delete(); app(AuditLogService::class)->log('module.deleted',$tables[$module],$id,['module'=>$module,'before'=>$before ? (array)$before : []]); return back()->with('success','Record deleted.');
    }
    public function toggle(Request $request,string $module,int $id){
        $tables=['brands'=>'brands','reviews'=>'reviews','coupons'=>'coupons','shipping'=>'shipping_methods','currencies'=>'currencies','payment-methods'=>'payment_methods','pages'=>'pages','group-buying'=>'group_buying_campaigns','live-commerce'=>'live_streams'];
        abort_unless(isset($tables[$module]),404);$table=$tables[$module];$row=DB::table($table)->where('id',$id)->first();abort_unless($row,404);
        if($module==='group-buying'){$next=$row->status==='active'?'draft':'active';$field='status';}
        elseif($module==='live-commerce'){$next=$row->status==='live'?'scheduled':'live';$field='status';}
        elseif($module==='reviews'){$next=$row->status==='approved'?'pending':'approved';$field='status';}
        elseif($module==='pages'){$next=((int)$row->status)===1?0:1;$field='status';}
        else{$field=property_exists($row,'enabled')?'enabled':'status';$current=(int)$row->{$field};$next=$current===1?0:1;}
        DB::table($table)->where('id',$id)->update([$field=>$next,'updated_at'=>now()]);
        app(AuditLogService::class)->log('module.toggled',$table,$id,['module'=>$module,'field'=>$field,'to'=>$next]);
        return back()->with('success','Status updated.');
    }
}