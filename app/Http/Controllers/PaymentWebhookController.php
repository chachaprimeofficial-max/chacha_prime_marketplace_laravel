<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, string $provider, PaymentService $payments)
    {
        abort_unless(in_array($provider,['pingpong','lianlianpay','worldfirst','alipay','wechat_pay','paypal'],true),404);

        $payload=$request->getContent();
        $signature=$request->header('X-Chacha-Signature') ?: $request->header('X-Webhook-Signature');
        abort_unless($payments->verifyWebhookSignature($provider,$payload,$signature),401,'Invalid webhook signature.');

        $data=$request->json()->all();
        $reference=$data['transaction_reference'] ?? $data['transaction_id'] ?? $data['id'] ?? null;
        $status=strtolower((string)($data['status'] ?? ''));
        $payment=null;

        if($reference){
            $payment=Payment::where('transaction_reference',$reference)->first();
        }
        if(!$payment && !empty($data['payment_id'])){
            $payment=Payment::find($data['payment_id']);
        }
        abort_unless($payment,404,'Payment not found.');

        if(in_array($status,['paid','completed','success','succeeded'],true)){
            $payments->markPaid($payment,$reference,$data);
        }elseif(in_array($status,['failed','cancelled','canceled','declined'],true)){
            $payments->markFailed($payment,$data);
        }

        return response()->json(['ok'=>true]);
    }
}
