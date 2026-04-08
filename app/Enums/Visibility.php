<?php

namespace App\Enums;

enum Visibility: string
{
    case PRIVATE = 'private';
    case SCORE_PUBLIC = 'score_public';
    case FULL_PUBLIC = 'full_public';

    public function label(): string
    {
        return __('labels.visibility.' . $this->value);
    }
}
