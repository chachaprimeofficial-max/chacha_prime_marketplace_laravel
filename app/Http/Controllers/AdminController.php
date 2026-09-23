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
    public function importCatalog(Request $request){
        $request->validate(['file'=>'required|file|mimes:csv,txt|max:20480']);
        $file=$request->file('file'); $path=$file->store('catalog-imports');
        $jobId=DB::table('catalog_imports')->insertGetId(['user_id'=>$request->user()->id,'source_type'=>'csv','source_name'=>$file->getClientOriginalName(),'file_path'=>$path,'status'=>'processing','created_at'=>now(),'updated_at'=>now()]);
        $handle=fopen($file->getRealPath(),'r'); $header=array_map(fn($v)=>Str::snake(trim((string)$v)), fgetcsv($handle) ?: []); $total=$ok=$failed=0; $errors=[];
        while(($row=fgetcsv($handle))!==false){$total++; $data=array_combine($header,$row); try{
            if(empty($data['name'])) throw new \RuntimeException('name is required');
            $vendorId=(int)($data['vendor_id']??0); $categoryId=!empty($data['category_id'])?(int)$data['category_id']:null; if(!$categoryId && !empty($data['category'])){$categoryId=DB::table('categories')->where('name',$data['category'])->value('id');}
            abort_unless($vendorId>0 && DB::table('vendors')->where('id',$vendorId)->exists(),422,'Invalid vendor_id');
            $existing=!empty($data['sku'])?Product::where('sku',$data['sku'])->first():null;
            $payload=['vendor_id'=>$vendorId,'category_id'=>$categoryId,'brand_id'=>!empty($data['brand_id'])?(int)$data['brand_id']:null,'name'=>trim($data['name']),'retail_price'=>(float)($data['retail_price']??0),'cost_price'=>isset($data['cost_price'])?(float)$data['cost_price']:null,'currency'=>strtoupper($data['currency']??'USD'),'stock'=>(float)($data['stock']??0),'description'=>$data['description']??null];
            if($existing){$existing->update($payload);$product=$existing;}else{$payload['slug']=Str::slug($payload['name']).'-'.Str::lower(Str::random(6));$payload['sku']=$data['sku']??('CP-'.strtoupper(Str::random(8)));$payload['status']='pending';$payload['stock_status']=$payload['stock']>0?'in_stock':'out_of_stock';$product=Product::create($payload);}
            if(!DB::table('product_identifiers')->where('product_id',$product->id)->exists()) DB::table('product_identifiers')->insert(['product_id'=>$product->id,'product_code'=>'CP-PROD-'.str_pad((string)$product->id,8,'0',STR_PAD_LEFT),'internal_sku'=>$product->sku,'barcode_value'=>'CP'.str_pad((string)$product->id,12,'0',STR_PAD_LEFT),'barcode_type'=>'CODE128','qr_token'=>bin2hex(random_bytes(20)),'created_at'=>now(),'updated_at'=>now()]); $ok++;
        }catch(\Throwable $e){$failed++;$errors[]=['row'=>$total,'error'=>$e->getMessage()];}}
        fclose($handle); DB::table('catalog_imports')->where('id',$jobId)->update(['status'=>$failed?'failed':'completed','total_rows'=>$total,'imported_rows'=>$ok,'failed_rows'=>$failed,'error_log'=>$errors?json_encode($errors):null,'updated_at'=>now()]); return back()->with('success',"CSV processed: {$ok} imported, {$failed} failed.");
    }
    public function aiProductBuilder(Request $request){
        $data=$request->validate(['input'=>'required|string|max:2000']); $prompt="Return JSON only with keys title,short_description,highlights,description,specifications,features,seo_title,seo_description,keywords,category_suggestion. Create marketplace product content from: ".$data['input']; $raw=app(\App\Services\GeminiService::class)->ask($prompt); $json=json_decode($raw,true); return response()->json(['ok'=>true,'data'=>$json,'raw'=>$raw]);
    }
    public function assignSubscription(Request $request){$d=$request->validate(['user_id'=>'required|exists:users,id','plan_id'=>'required|exists:subscription_plans,id','days'=>'nullable|integer|min:1|max:3650']);$days=(int)($d['days']??30);DB::table('user_subscriptions')->where('user_id',$d['user_id'])->where('status','active')->update(['status'=>'cancelled','updated_at'=>now()]);DB::table('user_subscriptions')->insert(['user_id'=>$d['user_id'],'plan_id'=>$d['plan_id'],'status'=>'active','starts_at'=>now(),'ends_at'=>now()->addDays($days),'created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Subscription assigned successfully.');}
    public function categoryAiBuilder(Request $request){$d=$request->validate(['input'=>'required|string|max:2000']);$prompt="Return JSON only with keys category_name,parent_category,attributes,filters,description,seo_title,seo_description. Design a marketplace category from: ".$d['input'];$raw=app(\App\Services\GeminiService::class)->ask($prompt);return response()->json(['ok'=>true,'data'=>json_decode($raw,true),'raw'=>$raw]);} public function saveAiCategory(Request $request){$d=$request->validate(['name'=>'required|string|max:150','parent_id'=>'nullable|exists:categories,id','description'=>'nullable|string','seo_title'=>'nullable|string|max:220','seo_description'=>'nullable|string|max:500','attributes'=>'nullable|string']);$slug=Str::slug($d['name']).'-'.Str::lower(Str::random(5));$categoryId=DB::table('categories')->insertGetId(['parent_id'=>$d['parent_id']??null,'name'=>$d['name'],'slug'=>$slug,'description'=>$d['description']??null,'seo_title'=>$d['seo_title']??null,'seo_description'=>$d['seo_description']??null,'metadata'=>json_encode(['source'=>'ai_category_builder']),'sort_order'=>0,'status'=>1,'created_at'=>now(),'updated_at'=>now()]);$attrs=json_decode($d['attributes']??'[]',true);if(is_array($attrs)){foreach($attrs as $idx=>$a){if(!is_array($a)||empty($a['name']))continue;DB::table('category_attributes')->insert(['category_id'=>$categoryId,'name'=>Str::limit((string)$a['name'],120,''),'field_type'=>in_array(($a['type']??'text'),['text','number','select','multiselect','boolean','date'],true)?$a['type']:'text','options'=>isset($a['options'])?json_encode($a['options']):null,'is_required'=>!empty($a['required'])?1:0,'sort_order'=>$idx,'created_at'=>now(),'updated_at'=>now()]);}}app(AuditLogService::class)->log('category.ai_created','Category',$categoryId,['name'=>$d['name']]);return back()->with('success','AI category approved and saved.');}
    public function labelSheet(Request $request){$ids=array_filter(array_map('intval',(array)$request->input('ids',[])));abort_unless(count($ids)>0 && count($ids)<=100,422,'Select 1 to 100 products.');$products=DB::table('products')->leftJoin('product_identifiers','products.id','=','product_identifiers.product_id')->whereIn('products.id',$ids)->select('products.id','products.name','products.sku','products.retail_price','products.currency','product_identifiers.product_code','product_identifiers.barcode_value','product_identifiers.qr_token')->get();return view('admin.label-sheet',compact('products'));}
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
        $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(6)); $data['sku']='CP-'.strtoupper(Str::random(8)); $data['status']='pending'; $data['stock_status']=$data['stock']>0?'in_stock':'out_of_stock'; $product=Product::create($data); DB::table('product_identifiers')->insert(['product_id'=>$product->id,'product_code'=>'CP-PROD-'.str_pad((string)$product->id,8,'0',STR_PAD_LEFT),'internal_sku'=>$product->sku,'barcode_value'=>'CP'.str_pad((string)$product->id,12,'0',STR_PAD_LEFT),'barcode_type'=>'CODE128','qr_token'=>bin2hex(random_bytes(20)),'created_at'=>now(),'updated_at'=>now()]); app(AuditLogService::class)->log('product.created','Product',$product->id,['name'=>$product->name,'vendor_id'=>$product->vendor_id,'status'=>$product->status]); return back()->with('success','Product created.');
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