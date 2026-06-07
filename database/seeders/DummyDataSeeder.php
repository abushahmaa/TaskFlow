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
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        $pms = User::role('project-manager')->get();
        $employees = User::role('employee')->get();

        if ($pms->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('No users found to assign dummy data.');
            return;
        }

        // Generate 10 Projects
        $projectNames = [
            'Marketing Campaign Q3',
            'Mobile App Launch',
            'Cloud Migration',
            'Website Redesign',
            'Database Optimization',
            'Security Audit 2025',
            'Customer Portal V2',
            'ERP Integration',
            'New Office Setup',
            'Social Media Automation'
        ];

        $projects = [];
        foreach ($projectNames as $index => $name) {
            $status = match(true) {
                $index < 2 => ProjectStatus::Planning->value,
                $index < 7 => ProjectStatus::Active->value,
                $index < 9 => ProjectStatus::Completed->value,
                default => ProjectStatus::Archived->value,
            };

            $startDate = Carbon::now()->subDays(rand(10, 60));
            $endDate = Carbon::now()->addDays(rand(-10, 60)); // Some in the past

            $projects[] = Project::create([
                'name' => $name,
                'description' => "Detailed description and requirements for $name.",
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'manager_id' => $pms->random()->id,
            ]);
        }

        // Generate 50 Tasks
        $taskVerbs = ['Design', 'Implement', 'Test', 'Review', 'Deploy', 'Analyze', 'Fix', 'Document'];
        $taskNouns = ['API', 'Frontend', 'Backend', 'Database', 'Server', 'UI Components', 'Security Rules', 'Auth System'];

        $tasks = [];
        foreach (range(1, 50) as $i) {
            $project = $projects[array_rand($projects)];
            $isOverdue = rand(1, 10) > 8; // 20% chance of being overdue
            
            $deadline = $isOverdue 
                ? Carbon::now()->subDays(rand(1, 10)) 
                : Carbon::now()->addDays(rand(1, 30));

            $statusOptions = TaskStatus::values();
            if ($isOverdue && in_array(TaskStatus::Completed->value, $statusOptions)) {
                $statusOptions = array_diff($statusOptions, [TaskStatus::Completed->value]); // Overdue tasks aren't completed
            }
            
            $status = $statusOptions[array_rand($statusOptions)];

            $tasks[] = Task::create([
                'name' => $taskVerbs[array_rand($taskVerbs)] . ' ' . $taskNouns[array_rand($taskNouns)],
                'description' => "Please complete this task according to the specification.",
                'priority' => TaskPriority::values()[array_rand(TaskPriority::values())],
                'status' => $status,
                'deadline' => $deadline,
                'estimated_hours' => rand(4, 40),
                'assigned_to' => $employees->random()->id,
                'project_id' => $project->id,
                'created_by' => $project->manager_id,
            ]);
        }

        // Generate Work Logs for Tasks
        foreach ($tasks as $task) {
            if (in_array($task->status->value, [TaskStatus::InProgress->value, TaskStatus::InReview->value, TaskStatus::Completed->value])) {
                $numLogs = rand(1, 3);
                for ($j = 0; $j < $numLogs; $j++) {
                    WorkLog::create([
                        'task_id' => $task->id,
                        'user_id' => $task->assigned_to,
                        'hours_worked' => rand(2, 8),
                        'description' => "Worked on " . strtolower($task->name) . " - phase " . ($j + 1),
                        'created_at' => Carbon::now()->subDays(rand(1, 10)),
                    ]);
                }
            }
        }

        $this->command->info('Dummy projects, tasks, and logs seeded!');
    }
}
