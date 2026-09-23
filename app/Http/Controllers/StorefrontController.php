<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('storefront.home');
    }
}
