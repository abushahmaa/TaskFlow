<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run in order: Roles → Users → Projects/Tasks
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            ProjectTaskSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}
