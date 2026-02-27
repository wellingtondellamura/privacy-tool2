<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Website / URL')
                    ->default(null),
                TextInput::make('icon')
                    ->label('Ícone')
                    ->default(null),
                TextInput::make('color')
                    ->label('Cor')
                    ->default(null),
                Select::make('owner_id')
                    ->label('Proprietário')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
