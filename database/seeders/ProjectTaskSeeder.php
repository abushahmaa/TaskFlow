<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Database\Seeder;

class ProjectTaskSeeder extends Seeder
{
    public function run(): void
    {
        $pm1      = User::where('email', 'alice.pm@taskflow.com')->first();
        $pm2      = User::where('email', 'bob.pm@taskflow.com')->first();
        $admin    = User::where('email', 'admin@taskflow.com')->first();
        $employees = User::role('employee')->get();

        if (!$pm1 || !$pm2 || $employees->isEmpty()) {
            $this->command->warn('Users not found — run UserSeeder first.');
            return;
        }

        // ── Projects ──────────────────────────────────────────────────────
        $project1 = Project::firstOrCreate(
            ['name' => 'E-Commerce Platform Redesign'],
            [
                'description' => 'Full redesign of the customer-facing e-commerce platform with modern UI/UX.',
                'start_date'  => now()->subDays(30),
                'end_date'    => now()->addDays(60),
                'status'      => ProjectStatus::Active->value,
                'manager_id'  => $pm1->id,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['name' => 'Internal HR Management System'],
            [
                'description' => 'Build an HR management system for employee records and payroll.',
                'start_date'  => now()->subDays(10),
                'end_date'    => now()->addDays(90),
                'status'      => ProjectStatus::Active->value,
                'manager_id'  => $pm2->id,
            ]
        );

        $project3 = Project::firstOrCreate(
            ['name' => 'Mobile App MVP'],
            [
                'description' => 'Build a minimum viable product for the mobile application.',
                'start_date'  => now()->addDays(5),
                'end_date'    => now()->addDays(120),
                'status'      => ProjectStatus::Planning->value,
                'manager_id'  => $pm1->id,
            ]
        );

        // ── Tasks for Project 1 ───────────────────────────────────────────
        $tasksData1 = [
            [
                'name'            => 'UI/UX Wireframe Design',
                'description'     => 'Create wireframes for all key pages.',
                'priority'        => TaskPriority::High->value,
                'status'          => TaskStatus::Completed->value,
                'deadline'        => now()->subDays(5),
                'estimated_hours' => 16,
                'assigned_to'     => $employees[0]->id,
            ],
            [
                'name'            => 'Frontend Development - Homepage',
                'description'     => 'Build the responsive homepage component.',
                'priority'        => TaskPriority::High->value,
                'status'          => TaskStatus::InProgress->value,
                'deadline'        => now()->addDays(7),
                'estimated_hours' => 24,
                'assigned_to'     => $employees[1]->id,
            ],
            [
                'name'            => 'Payment Gateway Integration',
                'description'     => 'Integrate Stripe payment gateway.',
                'priority'        => TaskPriority::Critical->value,
                'status'          => TaskStatus::ToDo->value,
                'deadline'        => now()->addDays(14),
                'estimated_hours' => 20,
                'assigned_to'     => $employees[2]->id,
            ],
            [
                'name'            => 'Backend API for Product Catalog',
                'description'     => 'REST API endpoints for product listing and details.',
                'priority'        => TaskPriority::Medium->value,
                'status'          => TaskStatus::InReview->value,
                'deadline'        => now()->addDays(3),
                'estimated_hours' => 30,
                'assigned_to'     => $employees[0]->id,
            ],
        ];

        foreach ($tasksData1 as $taskData) {
            Task::firstOrCreate(
                ['name' => $taskData['name'], 'project_id' => $project1->id],
                array_merge($taskData, ['project_id' => $project1->id, 'created_by' => $pm1->id])
            );
        }

        // ── Tasks for Project 2 ───────────────────────────────────────────
        $tasksData2 = [
            [
                'name'            => 'Employee Onboarding Module',
                'description'     => 'Build the employee onboarding workflow.',
                'priority'        => TaskPriority::High->value,
                'status'          => TaskStatus::InProgress->value,
                'deadline'        => now()->addDays(21),
                'estimated_hours' => 40,
                'assigned_to'     => $employees[3]->id,
            ],
            [
                'name'            => 'Payroll Calculation Engine',
                'description'     => 'Implement automated payroll calculation with tax support.',
                'priority'        => TaskPriority::Critical->value,
                'status'          => TaskStatus::ToDo->value,
                'deadline'        => now()->addDays(45),
                'estimated_hours' => 60,
                'assigned_to'     => $employees[4]->id,
            ],
        ];

        foreach ($tasksData2 as $taskData) {
            Task::firstOrCreate(
                ['name' => $taskData['name'], 'project_id' => $project2->id],
                array_merge($taskData, ['project_id' => $project2->id, 'created_by' => $pm2->id])
            );
        }

        // ── Sample Work Logs ──────────────────────────────────────────────
        $task1 = Task::where('name', 'Frontend Development - Homepage')->first();
        if ($task1) {
            WorkLog::firstOrCreate(
                ['task_id' => $task1->id, 'user_id' => $employees[1]->id, 'hours_worked' => 4],
                [
                    'description' => 'Set up the project structure and created the main layout component.',
                    'hours_worked' => 4,
                ]
            );

            WorkLog::firstOrCreate(
                ['task_id' => $task1->id, 'user_id' => $employees[1]->id, 'hours_worked' => 6],
                [
                    'description' => 'Implemented responsive navbar and hero section with animations.',
                    'hours_worked' => 6,
                ]
            );
        }

        $this->command->info('Projects, tasks, and work logs seeded successfully.');
    }
}
