<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function createPayment(Order $order, int $methodId): Payment
    {
        $method = \DB::table('payment_methods')->where('id',$methodId)->where('enabled',1)->first();
        abort_unless($method,422,'Selected payment method is unavailable.');

        return Payment::create([
            'order_id'=>$order->id,
            'user_id'=>$order->user_id,
            'method_id'=>$method->id,
            'transaction_reference'=>'CP-'.Str::upper(Str::random(20)),
            'amount'=>$order->grand_total,
            'currency'=>$order->currency,
            'status'=>'pending',
        ]);
    }

    public function markPaid(Payment $payment, ?string $reference=null, array $gatewayResponse=[]): Payment
    {
        $payment->update([
            'status'=>'paid',
            'transaction_reference'=>$reference ?: $payment->transaction_reference,
            'gateway_response'=>$gatewayResponse ?: null,
            'paid_at'=>now(),
        ]);
        $payment->order()->update(['payment_status'=>'paid']);
        return $payment->refresh();
    }

    public function markFailed(Payment $payment, array $gatewayResponse=[]): Payment
    {
        $payment->update(['status'=>'failed','gateway_response'=>$gatewayResponse ?: null]);
        return $payment->refresh();
    }

    public function enabledMethods()
    {
        return \DB::table('payment_methods')->where('enabled',1)->orderBy('name')->get();
    }
}
