<?php

namespace App\Services;

use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;

class AdminAiService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    public function ask(int $userId, string $message): string
    {
        $stats = [
            'users' => DB::table('users')->count(),
            'customers' => DB::table('users')->whereIn('role',['customer','b2b_customer'])->count(),
            'vendors' => DB::table('vendors')->count(),
            'pending_vendors' => DB::table('vendors')->where('status','pending')->count(),
            'products' => DB::table('products')->count(),
            'pending_products' => DB::table('products')->where('status','pending')->count(),
            'orders' => DB::table('orders')->count(),
            'pending_orders' => DB::table('orders')->where('status','pending')->count(),
            'paid_sales' => DB::table('orders')->where('payment_status','paid')->sum('grand_total'),
            'pending_payments' => DB::table('payments')->where('status','pending')->count(),
            'open_support' => DB::table('support_conversations')->whereIn('status',['open','pending'])->count(),
        ];

        $orders=DB::table('orders')->latest('id')->limit(20)->get(['id','order_number','status','payment_status','fulfillment_status','grand_total','currency','created_at']);
        $vendors=DB::table('vendors')->latest('id')->limit(20)->get(['id','business_name','status','verification_status','commission_rate','created_at']);
        $products=DB::table('products')->latest('id')->limit(30)->get(['id','vendor_id','name','sku','retail_price','currency','stock','status','created_at']);
        $support=DB::table('support_conversations')->latest('last_message_at')->limit(20)->get(['id','type','customer_id','vendor_id','subject','department','status','priority','assigned_to','last_message_at']);

        $prompt="You are Chacha Prime's internal admin copilot. Answer using ONLY the verified platform data below. You may summarize, explain trends visible in the supplied data, locate records, and suggest which admin screen to open. Never expose passwords, OTPs, TOTP secrets, card tokens, API keys, payment gateway secrets, or other credentials. Do not claim that you changed data or completed an action; you are read-only. If the requested information is not in the supplied data, say that it is not available in the current context. Be concise.

STATS:
".json_encode($stats)."
ORDERS:
".json_encode($orders)."
VENDORS:
".json_encode($vendors)."
PRODUCTS:
".json_encode($products)."
SUPPORT:
".json_encode($support)."

ADMIN QUESTION:
".$message;
        $answer=$this->gemini->ask($prompt);
        DB::table('ai_logs')->insert(['user_id'=>$userId,'context'=>'admin_copilot','provider'=>'gemini','model'=>config('services.gemini.model'),'prompt'=>$message,'response'=>$answer,'created_at'=>now()]);
        return $answer;
    }
}
