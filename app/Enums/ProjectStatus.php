<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning  = 'planning';
    case Active    = 'active';
    case Completed = 'completed';
    case Archived  = 'archived';

    public function label(): string
    {
        return match($this) {
            self::Planning  => 'Planning',
            self::Active    => 'Active',
            self::Completed => 'Completed',
            self::Archived  => 'Archived',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
