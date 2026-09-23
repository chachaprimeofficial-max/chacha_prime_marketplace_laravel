<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    public function index()
    {
        return view('storefront.ai-assistant');
    }

    public function ask(Request $request, GeminiService $gemini)
    {
        $data = $request->validate(['message' => 'required|string|max:1000']);
        $products = Product::where('status', 'published')
            ->select('id','name','short_description','retail_price','currency','stock','stock_status')
            ->latest()->limit(40)->get();

        $catalog = $products->map(fn($p) => "{$p->name} | {$p->retail_price} {$p->currency} | stock: {$p->stock_status} | {$p->short_description}")->implode("\n");
        $prompt = "You are Chacha Prime's shopping assistant. Help customers discover products using ONLY this catalog. Never invent products, prices, stock, discounts, policies, or delivery promises. If the catalog does not contain the answer, say so. Be concise and helpful.\n\nCATALOG:\n{$catalog}\n\nCUSTOMER:\n{$data['message']}";
        $answer=$gemini->ask($prompt); DB::table('ai_logs')->insert(['user_id'=>$request->user()?->id,'context'=>'storefront','provider'=>'gemini','model'=>config('services.gemini.model'),'prompt'=>$data['message'],'response'=>$answer,'created_at'=>now()]); return response()->json(['answer'=>$answer]);
    }
}
