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
        $map = [
            ResponseProfile::INFORMATION_QUALITY->value => [
                AnswerLevel::HIGH->value => 'Suficiente',
                AnswerLevel::MEDIUM->value => 'Insuficiente',
                AnswerLevel::LOW->value => 'Inexistente',
                AnswerLevel::OTHER->value => 'Outro',
            ],
            ResponseProfile::PRESENTATION_FORMAT->value => [
                AnswerLevel::HIGH->value => 'Apropriado',
                AnswerLevel::MEDIUM->value => 'Inapropriado', // The user prompt says medium -> Inapropriado, low -> Necessita melhorias
                AnswerLevel::LOW->value => 'Necessita melhorias',
                AnswerLevel::OTHER->value => 'Outro',
            ],
        ];

        // Let's re-check the user prompt for presentation_format labels
        // high   → Apropriado
        // medium → Inapropriado
        // low    → Necessita melhorias
        // other  → Outro

        return $map[$profile->value][$level->value] ?? 'Desconhecido';
    }

    /**
     * Get all options (value + label) for a given profile.
     */
    public static function optionsForProfile(ResponseProfile $profile): array
    {
        return collect(AnswerLevel::cases())
            ->map(fn($level) => [
                'value' => $level->value,
                'label' => self::resolve($level, $profile),
            ])->toArray();
    }
}
