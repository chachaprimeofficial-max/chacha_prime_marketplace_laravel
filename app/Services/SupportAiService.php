<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SupportAiService
{
    public function __construct(private GeminiService $gemini, private SupportService $support)
    {
    }

    public function reply(User $user, string $message, ?int $conversationId = null): array
    {
        $conversation = $conversationId
            ? DB::table('support_conversations')->where('id', $conversationId)->where('customer_id', $user->id)->where('type','customer_support')->first()
            : $this->support->conversationForUser($user->id);

        if (!$conversation) {
            $conversationId = $this->support->createConversation([
                'type' => 'customer_support',
                'customer_id' => $user->id,
                'subject' => 'AI Support Chat',
                'department' => 'customer_support',
                'status' => 'ai_handled',
                'priority' => 'normal',
                'ai_handled' => 1,
            ]);
            $conversation = DB::table('support_conversations')->where('id', $conversationId)->first();
        } else {
            $conversationId = (int) $conversation->id;
            if (in_array($conversation->status, ['open','pending'], true)) {
                $this->support->addMessage($conversationId, $user->id, 'customer', $message);
                return [
                    'answer' => 'Your case is already with our human support team. Please continue in Support Center so the assigned agent can reply there.',
                    'escalate' => true,
                    'department' => $conversation->department ?: 'customer_support',
                    'priority' => $conversation->priority ?: 'normal',
                    'reason' => 'Existing human support case is active.',
                    'conversation_id' => $conversationId,
                ];
            }
        }

        $this->support->addMessage($conversationId, $user->id, 'customer', $message);

        $context = $this->buildContext($user, $message, $conversationId);
        $schema = [
            'type' => 'object',
            'properties' => [
                'answer' => ['type' => 'string'],
                'escalate' => ['type' => 'boolean'],
                'department' => ['type' => 'string', 'enum' => ['customer_support','orders','payments','returns','technical','vendor_support']],
                'priority' => ['type' => 'string', 'enum' => ['low','normal','high','urgent']],
                'reason' => ['type' => 'string'],
            ],
            'required' => ['answer','escalate','department','priority','reason'],
        ];

        $prompt = <<<PROMPT
You are Chacha Prime's first-line customer support AI.
Use ONLY the verified marketplace context below. Never invent an order status, price, refund, stock value, policy, delivery promise, account fact, or staff decision.
You may explain known information, guide the customer through normal marketplace steps, identify the relevant product/order information, and collect the issue clearly.
You must NOT claim that money was refunded, an order was cancelled, a payment was changed, or an account was modified unless the context explicitly proves it.
If the customer asks for a human/agent, reports a payment/refund/security/account problem, has a complaint that needs staff action, or the context is insufficient to safely resolve the issue, set escalate=true.
If escalation is true, keep the answer short, explain that the case is being transferred to a human team, and choose the most relevant department.
Return JSON only.

VERIFIED CUSTOMER CONTEXT:
{$context}

CUSTOMER MESSAGE:
{$message}
PROMPT;

        $raw = $this->gemini->askJson($prompt, $schema);
        $data = json_decode($raw, true);

        if (!is_array($data) || !isset($data['answer'])) {
            $data = [
                'answer' => $raw,
                'escalate' => true,
                'department' => 'customer_support',
                'priority' => 'normal',
                'reason' => 'AI response could not be safely structured.',
            ];
        }

        $data['escalate'] = (bool) ($data['escalate'] ?? false);
        $data['department'] = in_array($data['department'] ?? '', ['customer_support','orders','payments','returns','technical','vendor_support'], true)
            ? $data['department'] : 'customer_support';
        $data['priority'] = in_array($data['priority'] ?? '', ['low','normal','high','urgent'], true)
            ? $data['priority'] : 'normal';

        $this->support->addMessage($conversationId, null, 'ai', (string) $data['answer'], [
            'escalate' => $data['escalate'],
            'department' => $data['department'],
        ]);

        if ($data['escalate']) {
            $staffIds = DB::table('users')->whereIn('role', ['super_admin','admin','staff'])->where('status','active')->pluck('id');
            foreach ($staffIds as $staffId) {
                DB::table('notifications')->insert([
                    'user_id' => $staffId,
                    'type' => 'support.escalated',
                    'title' => 'AI support case escalated',
                    'message' => 'Customer #'.$user->id.' needs human support for case #'.$conversationId.'.',
                    'data' => json_encode(['conversation_id'=>$conversationId,'department'=>$data['department']]),
                    'created_at' => now(),
                ]);
            }
            DB::table('support_conversations')->where('id', $conversationId)->update([
                'status' => 'open',
                'department' => $data['department'],
                'priority' => $data['priority'],
                'ai_handled' => 1,
                'escalation_reason' => $data['reason'] ?? null,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('support_conversations')->where('id', $conversationId)->update([
                'status' => 'ai_handled',
                'department' => $data['department'],
                'updated_at' => now(),
            ]);
        }

        DB::table('ai_logs')->insert([
            'user_id' => $user->id,
            'context' => 'support',
            'provider' => 'gemini',
            'model' => config('services.gemini.model'),
            'prompt' => $message,
            'response' => json_encode($data),
            'created_at' => now(),
        ]);

        return $data + ['conversation_id' => $conversationId];
    }

    private function buildContext(User $user, string $message, int $conversationId): string
    {
        $orders = DB::table('orders')
            ->where('user_id', $user->id)
            ->latest('id')->limit(12)->get([
                'id','order_number','status','payment_status','fulfillment_status',
                'currency','subtotal','discount_total','shipping_total','tax_total','grand_total','created_at'
            ]);

        $orderIds = $orders->pluck('id')->all();
        $items = $orderIds ? DB::table('order_items')->whereIn('order_id', $orderIds)->get([
            'order_id','vendor_id','product_id','product_name','sku','quantity','unit_price','subtotal','vendor_status'
        ]) : collect();

        $returns = DB::table('return_requests')
            ->where('customer_id', $user->id)->latest('id')->limit(10)->get([
                'id','order_id','order_item_id','vendor_id','reason','details','refund_amount','currency','status','resolution_note','created_at'
            ]);

        $messages = $this->support->messages($conversationId, 24)->map(fn($m) => [
            'sender' => $m->sender_role,
            'body' => $m->body,
            'time' => $m->created_at,
        ]);

        $tokens = preg_split('/\s+/u', mb_strtolower(trim($message)), -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_values(array_filter($tokens, fn($t) => mb_strlen($t) >= 3));
        $products = collect();
        if ($tokens) {
            $query = DB::table('products')->where('status','published');
            $query->where(function($q) use ($tokens) {
                foreach (array_slice($tokens, 0, 8) as $token) {
                    $like = '%'.$token.'%';
                    $q->orWhere('name','like',$like)->orWhere('sku','like',$like)->orWhere('short_description','like',$like);
                }
            });
            $products = $query->latest('id')->limit(20)->get([
                'id','name','sku','short_description','retail_price','wholesale_price','factory_price','currency','stock','stock_status','status'
            ]);
        }

        $cart = DB::table('carts')->where('user_id',$user->id)->first();
        $cartItems = $cart ? DB::table('cart_items')->where('cart_id',$cart->id)->limit(20)->get(['product_id','variant_id','quantity','unit_price']) : collect();

        return json_encode([
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
            ],
            'orders' => $orders,
            'order_items' => $items,
            'returns' => $returns,
            'matched_products' => $products,
            'cart' => $cartItems,
            'recent_support_messages' => $messages,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
