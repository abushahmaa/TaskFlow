<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskOverduePMMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Task $task) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[TaskFlow] PM Alert: Task \"{$this->task->name}\" is overdue"
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.overdue-alert-pm',
            with: ['task' => $this->task]
        );
    }
}
