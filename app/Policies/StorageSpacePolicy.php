<?php

namespace App\Policies;

use App\Models\StorageSpace;
use App\Models\User;
use App\Services\SpaceService;

class StorageSpacePolicy
{
    public function __construct(
        protected SpaceService $spaceService
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, StorageSpace $space): bool
    {
        return $this->spaceService->userCanAccess($space, $user);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, StorageSpace $space): bool
    {
        return $this->spaceService->userCanAccess($space, $user);
    }

    public function delete(User $user, StorageSpace $space): bool
    {
        return $this->spaceService->userCanAccess($space, $user);
    }
}
