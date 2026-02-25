<?php

namespace App\Policies;

use App\Models\Memo;
use App\Models\User;

class MemoPolicy
{
    public function view(User $user, Memo $memo): bool
    {
        return $memo->from_user_id === $user->id || $memo->to_user_id === $user->id;
    }

    public function update(User $user, Memo $memo): bool
    {
        return $memo->from_user_id === $user->id && $memo->status === Memo::STATUS_DRAFT;
    }

    public function delete(User $user, Memo $memo): bool
    {
        return $memo->from_user_id === $user->id;
    }
}
