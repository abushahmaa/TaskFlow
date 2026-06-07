<?php

namespace App\Enums;

enum TaskStatus: string
{
    case ToDo      = 'to_do';
    case InProgress = 'in_progress';
    case InReview  = 'in_review';
    case Completed = 'completed';
    case Blocked   = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::ToDo       => 'To Do',
            self::InProgress => 'In Progress',
            self::InReview   => 'In Review',
            self::Completed  => 'Completed',
            self::Blocked    => 'Blocked',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
