<?php

namespace App\Services;

use App\Enums\AnswerLevel;

class AnswerScoreResolver
{
    public static function resolve(AnswerLevel $level): ?int
    {
        return $level->score();
    }
}
