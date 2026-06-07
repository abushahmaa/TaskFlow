<?php

namespace App\Jobs;

use App\Mail\TaskOverdueEmployeeMail;
use App\Mail\TaskOverduePMMail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOverdueAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly Task $task) {}

    public function handle(): void
    {
        $task = $this->task->fresh(['assignee', 'project.manager']);

        if (!$task) {
            return;
        }

        // Email the employee
        if ($task->assignee) {
            Mail::to($task->assignee->email)
                ->send(new TaskOverdueEmployeeMail($task));
        }

        // Email the project manager
        if ($task->project && $task->project->manager) {
            Mail::to($task->project->manager->email)
                ->send(new TaskOverduePMMail($task));
        }
    }
}
