<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class SupportService
{
    public function channels(): array
    {
        $get = fn(string $key, string $default = '') => (string) (DB::table('settings')->where('setting_key', $key)->value('setting_value') ?? $default);

        return [
            'whatsapp' => $get('support_whatsapp'),
            'wechat' => $get('support_wechat'),
            'email' => $get('support_email', config('mail.from.address', 'support@chachaprime.com')),
        ];
    }

    public function conversationForUser(int $userId, ?int $id = null)
    {
        $q = DB::table('support_conversations')->where('customer_id', $userId)->where('type', 'customer_support');
        return $id
            ? $q->where('id', $id)->first()
            : $q->whereIn('status', ['ai_handled','open','pending'])->latest('id')->first();
    }

    public function conversationForVendor(int $vendorId, ?int $id = null)
    {
        $q = DB::table('support_conversations')->where('vendor_id', $vendorId)->where('type', 'vendor_support');
        return $id
            ? $q->where('id', $id)->first()
            : $q->whereIn('status', ['open','pending'])->latest('id')->first();
    }

    public function messages(int $conversationId, int $limit = 80)
    {
        return DB::table('support_messages as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.sender_user_id')
            ->where('m.conversation_id', $conversationId)
            ->select('m.*', 'u.name as sender_name', 'u.role as sender_user_role')
            ->orderBy('m.id')
            ->limit($limit)
            ->get();
    }

    public function createConversation(array $data): int
    {
        return (int) DB::table('support_conversations')->insertGetId($data + [
            'status' => 'open',
            'priority' => 'normal',
            'last_message_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function addMessage(int $conversationId, ?int $senderUserId, string $senderRole, string $body, array $metadata = []): int
    {
        $id = (int) DB::table('support_messages')->insertGetId([
            'conversation_id' => $conversationId,
            'sender_user_id' => $senderUserId,
            'sender_role' => $senderRole,
            'body' => $body,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
        ]);

        DB::table('support_conversations')->where('id', $conversationId)->update([
            'last_message_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    public function openCustomerSupport(User $user, string $subject = 'Customer Support'): int
    {
        $existing = $this->conversationForUser($user->id);
        if ($existing) {
            if ($existing->status === 'closed') {
                DB::table('support_conversations')->where('id', $existing->id)->update([
                    'status' => 'open',
                    'updated_at' => now(),
                ]);
            }
            return (int) $existing->id;
        }

        return $this->createConversation([
            'type' => 'customer_support',
            'customer_id' => $user->id,
            'subject' => $subject,
            'department' => 'customer_support',
            'ai_handled' => 0,
        ]);
    }

    public function openVendorSupport(Vendor $vendor, string $subject = 'Seller Center Support'): int
    {
        $existing = $this->conversationForVendor($vendor->id);
        if ($existing) {
            return (int) $existing->id;
        }

        return $this->createConversation([
            'type' => 'vendor_support',
            'vendor_id' => $vendor->id,
            'subject' => $subject,
            'department' => 'vendor_support',
            'ai_handled' => 0,
        ]);
    }

    public function openCustomerVendor(int $customerId, int $vendorId, ?int $orderId, string $subject): int
    {
        $existing = DB::table('support_conversations')
            ->where('type', 'customer_vendor')
            ->where('customer_id', $customerId)
            ->where('vendor_id', $vendorId)
            ->when($orderId, fn($q) => $q->where('order_id', $orderId))
            ->whereIn('status', ['open','pending'])
            ->latest('id')
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return $this->createConversation([
            'type' => 'customer_vendor',
            'customer_id' => $customerId,
            'vendor_id' => $vendorId,
            'order_id' => $orderId,
            'subject' => $subject,
            'department' => 'seller_support',
            'ai_handled' => 0,
        ]);
    }
}
