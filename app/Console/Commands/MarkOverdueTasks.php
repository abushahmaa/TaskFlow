<?php

namespace App\Console\Commands;

use App\Jobs\SendOverdueAlertJob;
use App\Models\Task;
use Illuminate\Console\Command;

class MarkOverdueTasks extends Command
{
    protected $signature   = 'tasks:mark-overdue';
    protected $description = 'Mark tasks past their deadline as overdue and dispatch alert jobs';

    public function handle(): int
    {
        $this->info('Scanning for overdue tasks...');

        $overdueTasks = Task::with(['assignee', 'project.manager'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['completed'])
            ->where('overdue_notified', false)
            ->get();

        $count = 0;
        foreach ($overdueTasks as $task) {
            // Dispatch overdue alert to employee + PM
            SendOverdueAlertJob::dispatch($task);

            // Mark as notified so we don't spam
            $task->update(['overdue_notified' => true]);

            $count++;
            $this->line("  → Overdue alert queued for task #{$task->id}: {$task->name}");
        }

        $this->info("Done. {$count} task(s) processed.");
        return self::SUCCESS;
    }
}
