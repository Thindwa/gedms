<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Ministry;
use App\Models\Section;
use App\Models\StorageSpace;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Resolves and authorizes storage spaces. Access is implicit:
 * - Personal: owner only
 * - Section: all members of that section
 * - Department: role-based—Director, Deputy Director, Department Administrator (in that dept)
 * - Ministry: role-based—Minister, Deputy Minister, Principal Secretary, System Admin (in that ministry)
 */
class SpaceService
{
    public function getOrCreatePersonalSpace(User $user): StorageSpace
    {
        $space = StorageSpace::where('type', StorageSpace::TYPE_PERSONAL)
            ->where('owner_user_id', $user->id)
            ->first();

        if ($space) {
            return $space;
        }

        return StorageSpace::create([
            'type' => StorageSpace::TYPE_PERSONAL,
            'owner_user_id' => $user->id,
            'name' => "{$user->name}'s Space",
            'is_active' => true,
        ]);
    }

    public function getOrCreateSectionSpace(Section $section): StorageSpace
    {
        $space = StorageSpace::where('type', StorageSpace::TYPE_SECTION)
            ->where('owner_section_id', $section->id)
            ->first();

        if ($space) {
            return $space;
        }

        return StorageSpace::create([
            'type' => StorageSpace::TYPE_SECTION,
            'owner_section_id' => $section->id,
            'name' => $section->name . ' Space',
            'is_active' => true,
        ]);
    }

    public function getOrCreateDepartmentSpace(Department $department): StorageSpace
    {
        $space = StorageSpace::where('type', StorageSpace::TYPE_DEPARTMENT)
            ->where('owner_department_id', $department->id)
            ->first();

        if ($space) {
            return $space;
        }

        return StorageSpace::create([
            'type' => StorageSpace::TYPE_DEPARTMENT,
            'owner_department_id' => $department->id,
            'name' => $department->name . ' Space',
            'is_active' => true,
        ]);
    }

    public function getOrCreateMinistrySpace(Ministry $ministry): StorageSpace
    {
        $space = StorageSpace::where('type', StorageSpace::TYPE_MINISTRY)
            ->where('owner_ministry_id', $ministry->id)
            ->first();

        if ($space) {
            return $space;
        }

        return StorageSpace::create([
            'type' => StorageSpace::TYPE_MINISTRY,
            'owner_ministry_id' => $ministry->id,
            'name' => $ministry->name . ' Space',
            'is_active' => true,
        ]);
    }

    public function spacesForUser(User $user): Collection
    {
        $spaces = collect();

        $personal = $this->getOrCreatePersonalSpace($user);
        $spaces->push($personal);

        if ($user->section_id) {
            $section = $user->section;
            if ($section) {
                $spaces->push($this->getOrCreateSectionSpace($section));
            }
        }

        if ($user->department_id) {
            $dept = $user->department;
            if ($dept) {
                if ($this->userCanViewDepartmentSpace($user)) {
                    $spaces->push($this->getOrCreateDepartmentSpace($dept));
                }
                // Department admins and directors see all section spaces in their department
                if ($user->can('manage-department') || $this->userCanViewDepartmentSpace($user)) {
                    foreach ($dept->sections as $section) {
                        $sectionSpace = $this->getOrCreateSectionSpace($section);
                        if (!$spaces->contains('id', $sectionSpace->id)) {
                            $spaces->push($sectionSpace);
                        }
                    }
                }
            }
        }

        if ($user->ministry_id) {
            $ministry = $user->ministry;
            if ($ministry && $this->userCanViewMinistrySpace($user)) {
                $spaces->push($this->getOrCreateMinistrySpace($ministry));
            }
        }

        return $spaces;
    }

    public function userCanViewDepartmentSpace(User $user): bool
    {
        if (!$user->department_id) {
            return false;
        }
        $roleNames = $user->getRoleNames();
        return $user->can('manage-department')
            || $roleNames->contains('Director')
            || $roleNames->contains('Deputy Director');
    }

    public function userCanViewMinistrySpace(User $user): bool
    {
        if (!$user->ministry_id) {
            return false;
        }
        $roleNames = $user->getRoleNames();
        return $user->can('manage-ministry')
            || $roleNames->contains('Minister')
            || $roleNames->contains('Deputy Minister')
            || $roleNames->contains('Principal Secretary')
            || $roleNames->contains('Director')
            || $roleNames->contains('Deputy Director');
    }

    public function userCanAccess(StorageSpace $space, User $user): bool
    {
        return match ($space->type) {
            StorageSpace::TYPE_PERSONAL => $space->owner_user_id === $user->id,
            StorageSpace::TYPE_SECTION => $space->owner_section_id === $user->section_id
                || ($user->can('manage-department') && $user->department_id && $space->ownerSection?->department_id === $user->department_id),
            StorageSpace::TYPE_DEPARTMENT => $space->owner_department_id === $user->department_id && $this->userCanViewDepartmentSpace($user),
            StorageSpace::TYPE_MINISTRY => $space->owner_ministry_id === $user->ministry_id && $this->userCanViewMinistrySpace($user),
            default => false,
        };
    }
}
