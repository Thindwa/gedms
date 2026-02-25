<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Ministry;
use App\Models\Section;
use App\Models\StorageSpace;
use App\Models\User;
use Illuminate\Database\Seeder;

class StorageSpaceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::all() as $user) {
            StorageSpace::firstOrCreate(
                [
                    'type' => StorageSpace::TYPE_PERSONAL,
                    'owner_user_id' => $user->id,
                ],
                [
                    'name' => "{$user->name}'s Space",
                    'is_active' => true,
                ]
            );
        }

        foreach (Section::all() as $section) {
            StorageSpace::firstOrCreate(
                [
                    'type' => StorageSpace::TYPE_SECTION,
                    'owner_section_id' => $section->id,
                ],
                [
                    'name' => $section->name . ' Space',
                    'is_active' => true,
                ]
            );
        }

        foreach (Department::all() as $dept) {
            StorageSpace::firstOrCreate(
                [
                    'type' => StorageSpace::TYPE_DEPARTMENT,
                    'owner_department_id' => $dept->id,
                ],
                [
                    'name' => $dept->name . ' Space',
                    'is_active' => true,
                ]
            );
        }

        foreach (Ministry::all() as $ministry) {
            StorageSpace::firstOrCreate(
                [
                    'type' => StorageSpace::TYPE_MINISTRY,
                    'owner_ministry_id' => $ministry->id,
                ],
                [
                    'name' => $ministry->name . ' Space',
                    'is_active' => true,
                ]
            );
        }
    }
}
