<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@taskflow.com'],
            [
                'name'      => 'System Admin',
                'password'  => Hash::make('password'),
                'phone'     => '+1-000-000-0000',
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // ── Project Managers ──────────────────────────────────────────────
        $pm1 = User::firstOrCreate(
            ['email' => 'alice.pm@taskflow.com'],
            [
                'name'      => 'Alice Johnson',
                'password'  => Hash::make('password'),
                'phone'     => '+1-111-111-0001',
                'is_active' => true,
            ]
        );
        $pm1->assignRole('project-manager');

        $pm2 = User::firstOrCreate(
            ['email' => 'bob.pm@taskflow.com'],
            [
                'name'      => 'Bob Williams',
                'password'  => Hash::make('password'),
                'phone'     => '+1-111-111-0002',
                'is_active' => true,
            ]
        );
        $pm2->assignRole('project-manager');

        // ── Employees ─────────────────────────────────────────────────────
        $employees = [
            ['name' => 'Charlie Brown',  'email' => 'charlie@taskflow.com'],
            ['name' => 'Diana Prince',   'email' => 'diana@taskflow.com'],
            ['name' => 'Edward Norton',  'email' => 'edward@taskflow.com'],
            ['name' => 'Fiona Green',    'email' => 'fiona@taskflow.com'],
            ['name' => 'George Miller',  'email' => 'george@taskflow.com'],
        ];

        foreach ($employees as $empData) {
            $emp = User::firstOrCreate(
                ['email' => $empData['email']],
                [
                    'name'      => $empData['name'],
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $emp->assignRole('employee');
        }

        $this->command->info('Users seeded: 1 admin, 2 PMs, 5 employees.');
    }
}
