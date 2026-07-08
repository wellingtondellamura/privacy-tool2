<?php

namespace App\Services;

use App\Enums\AnswerLevel;
use App\Enums\ResponseProfile;

class ResponseLabelResolver
{
    /**
     * Resolve the label for a given level based on the section's response profile.
     */
    public static function resolve(AnswerLevel $level, ResponseProfile $profile): string
    {
        return __('labels.response.' . $profile->value . '.' . $level->value);
    }

    /**
     * Get all options (value + label + labelKey) for a given profile.
     * `label` is the server-side translated string (legacy/fallback).
     * `labelKey` is the frontend i18n key so Vue can resolve it reactively.
     */
    public static function optionsForProfile(ResponseProfile $profile): array
    {
        return collect(AnswerLevel::cases())
            ->map(fn($level) => [
                'value'    => $level->value,
                'label'    => self::resolve($level, $profile),
                'labelKey' => 'labels.response.' . $profile->value . '.' . $level->value,
            ])->toArray();
    }
}
