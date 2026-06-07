<?php

namespace App\Models\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Task $task,
        public readonly int $hoursUntilDeadline
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Task Deadline Reminder: {$this->task->name}")
            ->markdown('emails.deadline-reminder', [
                'user' => $notifiable,
                'task' => $this->task,
                'hoursUntilDeadline' => $this->hoursUntilDeadline,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'                => 'deadline_reminder',
            'task_id'             => $this->task->id,
            'task_name'           => $this->task->name,
            'deadline'            => $this->task->deadline?->toISOString(),
            'hours_until_deadline' => $this->hoursUntilDeadline,
            'project_name'        => $this->task->project->name ?? null,
        ];
    }
}
