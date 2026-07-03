<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->required(),
                ...array_map(function ($locale, $label) {
                    return Textarea::make("text.{$locale}")
                        ->label("Texto ({$label})")
                        ->required($locale === config('app.fallback_locale'))
                        ->afterStateHydrated(function (Textarea $component, ?\Illuminate\Database\Eloquent\Model $record) use ($locale) {
                            if ($record) {
                                $component->state($record->getTranslation('text', $locale, false));
                            }
                        })
                        ->columnSpanFull();
                }, array_keys(config('app.available_locales', [])), config('app.available_locales', [])),
                ...array_map(function ($locale, $label) {
                    return Textarea::make("tooltip.{$locale}")
                        ->label("Tooltip ({$label})")
                        ->required(false)
                        ->afterStateHydrated(function (Textarea $component, ?\Illuminate\Database\Eloquent\Model $record) use ($locale) {
                            if ($record) {
                                $component->state($record->getTranslation('tooltip', $locale, false));
                            }
                        })
                        ->columnSpanFull();
                }, array_keys(config('app.available_locales', [])), config('app.available_locales', [])),
                ...array_map(function ($locale, $label) {
                    return Textarea::make("good_practice_example.{$locale}")
                        ->label("Exemplo de Boa Prática ({$label})")
                        ->required(false)
                        ->afterStateHydrated(function (Textarea $component, ?\Illuminate\Database\Eloquent\Model $record) use ($locale) {
                            if ($record) {
                                $component->state($record->getTranslation('good_practice_example', $locale, false));
                            }
                        })
                        ->columnSpanFull();
                }, array_keys(config('app.available_locales', [])), config('app.available_locales', [])),
                ...array_map(function ($locale, $label) {
                    return Textarea::make("bad_practice_example.{$locale}")
                        ->label("Exemplo de Má Prática ({$label})")
                        ->required(false)
                        ->afterStateHydrated(function (Textarea $component, ?\Illuminate\Database\Eloquent\Model $record) use ($locale) {
                            if ($record) {
                                $component->state($record->getTranslation('bad_practice_example', $locale, false));
                            }
                        })
                        ->columnSpanFull();
                }, array_keys(config('app.available_locales', [])), config('app.available_locales', [])),
                TextInput::make('order')
                    ->label('Ordem')
                    ->required()
                    ->numeric(),
            ]);
    }
}
