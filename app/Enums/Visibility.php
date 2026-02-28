<?php

namespace App\Enums;

enum Visibility: string
{
    case PRIVATE = 'private';
    case SCORE_PUBLIC = 'score_public';
    case FULL_PUBLIC = 'full_public';

    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => 'Privado',
            self::SCORE_PUBLIC => 'Apenas Score',
            self::FULL_PUBLIC => 'Relatório Completo',
        };
    }
}
