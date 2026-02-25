<?php

namespace App\Services;

use App\Models\File;
use App\Models\Memo;
use App\Models\User;

class MemoService
{
    public function __construct(protected AuditService $auditService) {}

    public function create(array $data, User $user): Memo
    {
        $memo = Memo::create([
            'direction' => $data['direction'],
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'from_user_id' => $user->id,
            'to_user_id' => $data['to_user_id'] ?? null,
            'ministry_id' => $data['ministry_id'] ?? $user->ministry_id,
            'department_id' => $data['department_id'] ?? $user->department_id,
            'file_id' => $data['file_id'] ?? null,
            'requires_approval' => $data['requires_approval'] ?? false,
            'status' => Memo::STATUS_DRAFT,
        ]);

        $this->auditService->log('memo.created', Memo::class, $memo->id, null, ['title' => $memo->title]);
        return $memo;
    }

    public function send(Memo $memo, User $user): Memo
    {
        $memo->from_user_id === $user->id || abort(403);
        $memo->status === Memo::STATUS_DRAFT || abort(400, 'Only draft memos can be sent');

        $memo->update([
            'status' => Memo::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->auditService->log('memo.sent', Memo::class, $memo->id, null, ['to_user_id' => $memo->to_user_id]);
        return $memo->fresh();
    }

    public function acknowledge(Memo $memo, User $user): Memo
    {
        $memo->to_user_id === $user->id || abort(403);
        $memo->status !== Memo::STATUS_SENT && abort(400, 'Memo must be sent first');

        $memo->update([
            'status' => Memo::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now(),
        ]);

        $this->auditService->log('memo.acknowledged', Memo::class, $memo->id, null, []);
        return $memo->fresh();
    }
}
