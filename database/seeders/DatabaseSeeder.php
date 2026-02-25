<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MinistriesSeeder::class,
            DepartmentsSeeder::class,
            SectionsSeeder::class,
            UsersSeeder::class,
            EdmsSeeder::class, // Adds admin@example.com, officer@example.com etc for quick dev login
            AssignSpatieRolesSeeder::class, // Syncs role column to Spatie roles for all users
            StorageSpaceSeeder::class,
            SensitivityLevelSeeder::class,
            DocumentTypeSeeder::class,
            WorkflowSeeder::class,
            RetentionRuleSeeder::class,
        ]);
    }
}
