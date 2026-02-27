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
                Textarea::make('text')
                    ->label('Texto')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->label('Ordem')
                    ->required()
                    ->numeric(),
            ]);
    }
}
