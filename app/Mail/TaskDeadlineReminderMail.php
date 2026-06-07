<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly int  $hoursUntilDeadline
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[TaskFlow] Reminder: \"{$this->task->name}\" due in {$this->hoursUntilDeadline} hour(s)"
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.deadline-reminder',
            with: [
                'task'                => $this->task,
                'hoursUntilDeadline'  => $this->hoursUntilDeadline,
            ]
        );
    }
}
