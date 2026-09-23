<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function users(){return view('admin.users',['users'=>User::latest()->paginate(20)]);}
    public function vendors(){return view('admin.vendors',['vendors'=>Vendor::with('user')->latest()->paginate(20)]);}
    public function products(){return view('admin.products',['products'=>Product::with(['vendor','category'])->latest()->paginate(20)]);}
    public function categories(){return view('admin.categories',['categories'=>Category::latest()->paginate(20)]);}

    public function updateUser(Request $request, User $user){
        $data=$request->validate(['status'=>'required|in:active,blocked,suspended','role'=>'required|in:super_admin,admin,vendor,b2b_customer,customer,affiliate,courier,streamer']);
        $user->update($data); return back()->with('success','User updated.');
    }
    public function updateVendor(Request $request, Vendor $vendor){
        $data=$request->validate(['status'=>'required|in:pending,active,suspended,rejected','verification_status'=>'required|in:pending,verified,rejected']);
        $vendor->update($data); return back()->with('success','Vendor updated.');
    }
    public function storeProduct(Request $request){
        $data=$request->validate(['vendor_id'=>'required|integer','category_id'=>'nullable|integer','name'=>'required|string|max:255','retail_price'=>'required|numeric|min:0','currency'=>'required|string|max:8','stock'=>'required|numeric|min:0','description'=>'nullable|string']);
        $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(6)); $data['sku']='CP-'.strtoupper(Str::random(8)); $data['status']='active'; Product::create($data); return back()->with('success','Product created.');
    }
    public function storeCategory(Request $request){
        $data=$request->validate(['name'=>'required|string|max:120','parent_id'=>'nullable|integer','description'=>'nullable|string']);
        $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5)); $data['status']='active'; Category::create($data); return back()->with('success','Category created.');
    }
}