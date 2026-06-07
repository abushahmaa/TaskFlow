<?php

namespace App\Models\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Task $task,
        public readonly string $recipientType = 'employee' // 'employee' | 'manager'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $view = $this->recipientType === 'manager'
            ? 'emails.overdue-alert-pm'
            : 'emails.overdue-alert-employee';

        return (new MailMessage)
            ->subject("OVERDUE Task: {$this->task->name}")
            ->markdown($view, [
                'user' => $notifiable,
                'task' => $this->task,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'task_overdue',
            'task_id'        => $this->task->id,
            'task_name'      => $this->task->name,
            'deadline'       => $this->task->deadline?->toISOString(),
            'project_name'   => $this->task->project->name ?? null,
            'recipient_type' => $this->recipientType,
        ];
    }
}
