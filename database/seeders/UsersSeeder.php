<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the System Administrator user only.
 * Table: users
 * Password: password
 */
class UsersSeeder extends Seeder
{
    private array $emailIndex = [];

    private string $hashedPassword;

    public function run(): void
    {
        $this->emailIndex = [];
        $this->userBatch = [];
        $this->hashedPassword = Hash::make('password');

        // System Admin: global, no ministry/department/section
        $this->insertUser([
            'name' => 'System Administrator',
            'email' => 'system.admin@edms.gov.mw',
            'role' => 'System Admin',
            'ministry_id' => null,
            'department_id' => null,
            'section_id' => null,
        ]);

        $this->flushUserBatch();
        return;

        $ministries = DB::table('ministries')->get();
        $departments = DB::table('departments')->get()->groupBy('ministry_id');
        $sections = DB::table('sections')->get()->groupBy('department_id');

        // Full hierarchy only for these 3 ministries; others get Minister, PS, Auditor only
        $focusMinistries = ['Ministry of Information and Digitalization', 'Ministry of Justice and Constitutional Affairs', 'Ministry of Agriculture, Irrigation and Water Development'];

        foreach ($ministries as $ministry) {
            $ministryDepts = $departments->get($ministry->id) ?? collect();
            $isFocus = in_array($ministry->name, $focusMinistries);

            // Minister: 1 per ministry
            $this->insertUser([
                'name' => $this->personName($ministry->name, 'Minister'),
                'email' => $this->uniqueEmail($this->slug($ministry->name) . '.minister'),
                'role' => 'Minister',
                'ministry_id' => $ministry->id,
                'department_id' => null,
                'section_id' => null,
            ]);

            // Ministry PS: 1 per ministry
            $this->insertUser([
                'name' => $this->personName($ministry->name, 'PS'),
                'email' => $this->uniqueEmail($this->slug($ministry->name) . '.ps'),
                'role' => 'Ministry PS',
                'ministry_id' => $ministry->id,
                'department_id' => null,
                'section_id' => null,
            ]);

            // Auditor: 1 per ministry
            $this->insertUser([
                'name' => $this->personName($ministry->name, 'Auditor'),
                'email' => $this->uniqueEmail($this->slug($ministry->name) . '.auditor'),
                'role' => 'Auditor',
                'ministry_id' => $ministry->id,
                'department_id' => null,
                'section_id' => null,
            ]);

            // Per-department users (full hierarchy for focus ministries, 1 dept for others)
            $depts = $isFocus ? $ministryDepts : $ministryDepts->take(1);

            foreach ($depts as $dept) {
                $deptSections = $sections->get($dept->id) ?? collect();
                $firstSection = $deptSections->first();
                $deptSlug = $this->slug($dept->name);

                // Director: 1 per department
                $this->insertUser([
                    'name' => $this->personName($dept->name, 'Director'),
                    'email' => $this->uniqueEmail($deptSlug . '.director'),
                    'role' => 'Director',
                    'ministry_id' => $ministry->id,
                    'department_id' => $dept->id,
                    'section_id' => null,
                ]);

                // Department PS: 1 per department
                $this->insertUser([
                    'name' => $this->personName($dept->name, 'Dept PS'),
                    'email' => $this->uniqueEmail($deptSlug . '.deptps'),
                    'role' => 'Department PS',
                    'ministry_id' => $ministry->id,
                    'department_id' => $dept->id,
                    'section_id' => null,
                ]);

                // Department Admin: 1 per department
                $this->insertUser([
                    'name' => $this->personName($dept->name, 'Dept Admin'),
                    'email' => $this->uniqueEmail($deptSlug . '.admin'),
                    'role' => 'Department Admin',
                    'ministry_id' => $ministry->id,
                    'department_id' => $dept->id,
                    'section_id' => null,
                ]);

                // Chief Officer: 1 per department
                $this->insertUser([
                    'name' => $this->personName($dept->name, 'Chief Officer'),
                    'email' => $this->uniqueEmail($deptSlug . '.chief'),
                    'role' => 'Chief Officer',
                    'ministry_id' => $ministry->id,
                    'department_id' => $dept->id,
                    'section_id' => null,
                ]);

                // Records Officer: 1 per department
                $this->insertUser([
                    'name' => $this->personName($dept->name, 'Records Officer'),
                    'email' => $this->uniqueEmail($deptSlug . '.records'),
                    'role' => 'Records Officer',
                    'ministry_id' => $ministry->id,
                    'department_id' => $dept->id,
                    'section_id' => $firstSection?->id,
                ]);

                // Officer: 1 per section
                foreach ($deptSections as $section) {
                    $this->insertUser([
                        'name' => $this->personName($section->name, 'Officer'),
                        'email' => $this->uniqueEmail($deptSlug . '_sec' . $section->id . '_officer'),
                        'role' => 'Officer',
                        'ministry_id' => $ministry->id,
                        'department_id' => $dept->id,
                        'section_id' => $section->id,
                    ]);
                }
            }
        }

        $this->flushUserBatch();
    }

    private function insertUser(array $data): void
    {
        $email = $data['email'];
        $role = $data['role'];
        unset($data['role']);

        $payload = [
            'name' => $data['name'],
            'email' => $email,
            'password' => $this->hashedPassword,
            'role' => $role,
            'ministry_id' => $data['ministry_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'section_id' => $data['section_id'] ?? null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->userBatch[] = $payload;
    }

    private function flushUserBatch(): void
    {
        if (empty($this->userBatch)) {
            return;
        }
        foreach ($this->userBatch as $row) {
            if (DB::table('users')->where('email', $row['email'])->exists()) {
                DB::table('users')->where('email', $row['email'])->update($row);
            } else {
                DB::table('users')->insert($row);
            }
        }
        $this->userBatch = [];
    }

    private function uniqueEmail(string $base): string
    {
        $base = str_replace([' ', "'", '.'], '', $base);
        $i = ($this->emailIndex[$base] ?? 0) + 1;
        $this->emailIndex[$base] = $i;
        $suffix = $i > 1 ? $i : '';
        return $base . $suffix . '@edms.gov.mw';
    }

    private function slug(string $name): string
    {
        return strtolower(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 12));
    }

    private function personName(string $context, string $role): string
    {
        $names = ['John Banda', 'Mary Phiri', 'Peter Mwale', 'Sarah Tembo', 'James Khonje', 'Alice Nkhoma', 'Michael Zulu', 'Grace Gondwe', 'Patrick Mbewe', 'Ellen Chiumia'];
        $key = crc32($context . $role) % count($names);
        return $names[$key];
    }
}
