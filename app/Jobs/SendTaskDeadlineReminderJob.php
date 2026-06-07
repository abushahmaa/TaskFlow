<?php

namespace App\Jobs;

use App\Mail\TaskDeadlineReminderMail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaskDeadlineReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Task $task,
        public readonly int  $hoursUntilDeadline
    ) {}

    public function handle(): void
    {
        // Reload task to get fresh status
        $task = $this->task->fresh();

        // Skip if task is already completed
        if (!$task || $task->status->value === 'completed') {
            return;
        }

        // Skip if assignee has been unset
        if (!$task->assigned_to || !$task->assignee) {
            return;
        }

        Mail::to($task->assignee->email)
            ->send(new TaskDeadlineReminderMail($task, $this->hoursUntilDeadline));
    }
}
