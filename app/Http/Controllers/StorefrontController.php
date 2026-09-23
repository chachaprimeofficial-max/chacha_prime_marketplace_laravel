<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
class StorefrontController extends Controller {
 public function home(){return view('storefront.home',['featured'=>Product::where('status','published')->latest()->limit(8)->get()]);}
 public function shop(Request $request){$q=$request->get('q');$products=Product::where('status','published')->when($q,fn($x)=>$x->where(fn($y)=>$y->where('name','like',"%$q%")->orWhere('description','like',"%$q%")))->latest()->paginate(24)->withQueryString();return view('storefront.shop',compact('products','q'));}
 public function product(int $id){$product=Product::where('status','published')->findOrFail($id);return view('storefront.product',compact('product'));}
 public function cart(Request $request){$cart=$request->session()->get('cart',[]);$products=Product::whereIn('id',array_keys($cart))->get();$total=$products->sum(fn($p)=>$p->price*($cart[$p->id]??0));return view('storefront.cart',compact('products','cart','total'));}
 public function addToCart(Request $request,int $id){$p=Product::where('status','published')->findOrFail($id);$cart=$request->session()->get('cart',[]);$cart[$id]=($cart[$id]??0)+max(1,(int)$request->input('quantity',1));$request->session()->put('cart',$cart);return back()->with('success',$p->name.' added to cart.');}
 public function removeFromCart(Request $request,int $id){$cart=$request->session()->get('cart',[]);unset($cart[$id]);$request->session()->put('cart',$cart);return back();}
}