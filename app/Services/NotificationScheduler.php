<?php

namespace App\Services;

use App\Jobs\SendTaskDeadlineReminderJob;
use App\Models\Task;
use Carbon\Carbon;

class NotificationScheduler
{
    /**
     * Dispatches deadline reminder jobs for a task at 48h, 24h, 12h, 1h intervals.
     */
    public function scheduleReminders(Task $task): void
    {
        if (!$task->deadline) {
            return;
        }

        $deadline = Carbon::parse($task->deadline);
        $now      = now();

        $intervals = [48, 24, 12, 1];

        foreach ($intervals as $hours) {
            $dispatchAt = $deadline->copy()->subHours($hours);

            if ($dispatchAt->greaterThan($now)) {
                $delay = $now->diffInSeconds($dispatchAt, false);
                SendTaskDeadlineReminderJob::dispatch($task, $hours)
                    ->delay(now()->addSeconds($delay));
            }
        }
    }

    /**
     * Cancels all pending reminders (via model-based unique job key awareness).
     * Note: For a simple implementation we rely on the job checking task status before sending.
     */
    public function cancelReminders(Task $task): void
    {
        // Jobs are fire-and-forget queued; they check task status before emailing.
        // No explicit cancellation needed — each job verifies if task is still pending.
    }
}
