<?php

namespace App\Console\Commands;

use App\Jobs\SendTaskDeadlineReminderJob;
use App\Models\Task;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'reminders:send';
    protected $description = 'Dispatch deadline reminder jobs for tasks due within the next 48 hours';

    public function handle(): int
    {
        $this->info('Checking for upcoming task deadlines...');

        $thresholds = [48, 24, 12, 1];

        foreach ($thresholds as $hours) {
            $windowStart = now()->addHours($hours)->subMinutes(8);  // ±8 min window
            $windowEnd   = now()->addHours($hours)->addMinutes(8);

            $tasks = Task::with(['assignee', 'project'])
                ->whereNotNull('assigned_to')
                ->whereNotNull('deadline')
                ->whereNotIn('status', ['completed'])
                ->whereBetween('deadline', [$windowStart, $windowEnd])
                ->get();

            foreach ($tasks as $task) {
                SendTaskDeadlineReminderJob::dispatch($task, $hours);
                $this->line("  → Dispatched {$hours}h reminder for task #{$task->id}: {$task->name}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
