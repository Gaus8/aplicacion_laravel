<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Str;

class AuditLogger
{
    private const SAFE_METADATA_KEYS = [
        'media_id',
        'email_sent_id',
        'smtp_active',
        'smtp_authentication_required',
    ];

    public function record(
        string $event,
        string $status = 'success',
        ?User $actor = null,
        ?string $subject = null,
        array $metadata = [],
    ): AuditLog {
        $entry = new AuditLog();
        $entry->actor_user_id = $actor?->getKey();
        $entry->actor_name = $actor ? Str::limit($actor->name, 255, '') : null;
        $entry->event = Str::limit($event, 120, '');
        $entry->status = in_array($status, ['success', 'failure', 'warning', 'blocked'], true) ? $status : 'warning';
        $entry->subject = $subject === null ? null : Str::limit($subject, 255, '');
        $entry->ip_address = request()->ip();
        $entry->user_agent = mb_substr((string) request()->userAgent(), 0, 512);
        $entry->metadata = array_intersect_key($metadata, array_flip(self::SAFE_METADATA_KEYS));
        $entry->created_at = now();
        $entry->save();

        return $entry;
    }
}
