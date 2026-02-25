<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Centralized audit logging. All critical actions flow through here.
 * Logs are immutable and queryable for accountability.
 */
class AuditService
{
    public function log(
        string $action,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ministry_id_scope' => $user?->ministry_id,
            'department_id_scope' => $user?->department_id,
        ]);
    }

}
