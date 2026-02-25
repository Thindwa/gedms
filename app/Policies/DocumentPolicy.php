<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-documents');
    }

    public function view(User $user, Document $document): bool
    {
        if (!$user->can('view-documents')) {
            return false;
        }
        return $document->ministry_id === $user->ministry_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create-documents');
    }

    public function update(User $user, Document $document): bool
    {
        if (!$user->can('edit-documents')) {
            return false;
        }
        if ($document->ministry_id !== $user->ministry_id) {
            return false;
        }
        return $document->canEdit($user);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can('edit-documents') && $document->ministry_id === $user->ministry_id;
    }

    public function approve(User $user, Document $document): bool
    {
        return $user->can('approve-documents') && $document->ministry_id === $user->ministry_id;
    }
}
