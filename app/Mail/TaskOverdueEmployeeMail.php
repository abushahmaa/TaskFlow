<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskOverdueEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Task $task) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[TaskFlow] OVERDUE: Your task \"{$this->task->name}\" is past its deadline"
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.overdue-alert-employee',
            with: ['task' => $this->task]
        );
    }
}
