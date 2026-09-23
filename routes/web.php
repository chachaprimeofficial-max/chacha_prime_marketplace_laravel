<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
Route::get('/',fn()=>view('storefront.home'))->name('home');
Route::get('/health',fn()=>response()->json(['status'=>'ok','application'=>config('app.name'),'version'=>app()->version()]))->name('health');
Route::prefix('auth')->name('auth.')->group(function(){
 Route::get('/login',[AuthController::class,'showLogin'])->name('login');
 Route::post('/login',[AuthController::class,'login'])->name('login.submit');
 Route::get('/register',[AuthController::class,'showRegister'])->name('register');
 Route::post('/register',[AuthController::class,'register'])->name('register.submit');
 Route::get('/otp',[AuthController::class,'otp'])->name('otp');
 Route::post('/otp',[AuthController::class,'verifyOtp'])->name('otp.verify');
 Route::get('/totp',[AuthController::class,'totp'])->name('totp');
 Route::post('/totp',[AuthController::class,'verifyTotp'])->name('totp.verify');
 Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
});
Route::get('/vendor/register',fn()=>redirect()->route('auth.register',['type'=>'vendor']))->name('vendor.register');
Route::middleware(['auth','role:vendor'])->prefix('vendor')->name('vendor.')->group(function(){Route::view('/dashboard','vendor.dashboard')->name('dashboard');});
Route::middleware(['auth','role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function(){
 Route::get('/',fn()=>redirect()->route('admin.dashboard'));
 Route::get('/dashboard',fn()=>view('admin.dashboard'))->name('dashboard');
 Route::get('/users',[AdminController::class,'users'])->name('users');
 Route::post('/users/{user}',[AdminController::class,'updateUser'])->name('users.update');
 Route::get('/vendors',[AdminController::class,'vendors'])->name('vendors');
 Route::post('/vendors/{vendor}',[AdminController::class,'updateVendor'])->name('vendors.update');
 Route::get('/products',[AdminController::class,'products'])->name('products');
 Route::post('/products',[AdminController::class,'storeProduct'])->name('products.store');
 Route::get('/categories',[AdminController::class,'categories'])->name('categories');
 Route::post('/categories',[AdminController::class,'storeCategory'])->name('categories.store');
});
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function(){Route::view('/dashboard','customer.dashboard')->name('dashboard');});