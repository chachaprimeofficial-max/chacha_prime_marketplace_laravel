<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('storefront.home');
})->name('home');

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'application' => config('app.name'),
    'version' => app()->version(),
]))->name('health');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
});

Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::view('/register', 'vendor.register')->name('register');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
});
