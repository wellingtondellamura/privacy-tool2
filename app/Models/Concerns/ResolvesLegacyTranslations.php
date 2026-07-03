<?php

namespace App\Models\Concerns;

trait ResolvesLegacyTranslations
{
    /**
     * Override toArray() to replace Spatie's empty-array serialization of plain-string
     * translatable fields with the accessor-resolved string value.
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        foreach ($this->translatable as $key) {
            $resolved = $this->getAttribute($key);
            $array[$key] = $resolved;
        }

        return $array;
    }

    protected function resolveTranslatedValue(string $attribute, mixed $value): ?string
    {
        $rawValue = $this->getRawOriginal($attribute);

        if (is_string($rawValue) && $rawValue !== '') {
            $decoded = json_decode($rawValue, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return $rawValue;
            }

            foreach ($this->translationLocaleCandidates() as $locale) {
                if (!empty($decoded[$locale]) && is_string($decoded[$locale])) {
                    return $decoded[$locale];
                }
            }

            foreach ($decoded as $translation) {
                if (is_string($translation) && $translation !== '') {
                    return $translation;
                }
            }
        }

        if (is_string($value) && $value !== '') {
            return $value;
        }

        return null;
    }

    protected function translationLocaleCandidates(): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        $availableLocales = array_keys(config('app.available_locales', []));

        return array_values(array_unique(array_filter(array_merge([
            $locale,
            is_string($locale) ? str_replace('-', '_', $locale) : null,
            $fallbackLocale,
            is_string($fallbackLocale) ? str_replace('-', '_', $fallbackLocale) : null,
        ], $availableLocales))));
    }
}