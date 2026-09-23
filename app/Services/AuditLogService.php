<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $metadata = []
    ): void {
        $request = request();

        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => json_encode($this->sanitize($metadata), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
        ]);
    }

    public function sanitize(array $data): array
    {
        $blocked = [
            'password',
            'password_confirmation',
            'remember_token',
            'totp_secret',
            'card_token',
            'cvv',
            'secret',
            'api_key',
            'api_secret',
            'webhook_secret',
            'client_secret',
            'access_token',
            'refresh_token',
        ];

        $clean = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $blocked, true)) {
                $clean[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
            } elseif (is_object($value)) {
                $clean[$key] = '[OBJECT]';
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}
