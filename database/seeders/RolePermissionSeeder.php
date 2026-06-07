<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define Permissions ─────────────────────────────────────────────
        $permissions = [
            // User management
            'users.view', 'users.create', 'users.update', 'users.delete',

            // Project management
            'projects.view', 'projects.create', 'projects.update', 'projects.delete',
            'projects.archive',

            // Task management
            'tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete',
            'tasks.assign',

            // Work logs
            'work-logs.view', 'work-logs.create', 'work-logs.update',
            'work-logs.reply',

            // Reports
            'reports.view',

            // Audit logs
            'audit-logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // ── Create Roles & Assign Permissions ──────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions($permissions); // Admin gets all permissions

        $pm = Role::firstOrCreate(['name' => 'project-manager', 'guard_name' => 'api']);
        $pm->syncPermissions([
            'projects.view', 'projects.update',
            'tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete', 'tasks.assign',
            'work-logs.view', 'work-logs.reply',
            'reports.view',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'api']);
        $employee->syncPermissions([
            'tasks.view', 'tasks.update',
            'work-logs.view', 'work-logs.create', 'work-logs.update',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
