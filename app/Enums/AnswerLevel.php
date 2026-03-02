<?php

namespace App\Enums;

enum AnswerLevel: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case OTHER = 'other';

    public function score(): ?int
    {
        return match ($this) {
            self::HIGH => 100,
            self::MEDIUM => 50,
            self::LOW => 0,
            self::OTHER => null,
        };
    }
}
