<?php

namespace App\Filament\Resources\ResultSnapshots\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ResultSnapshotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inspection_id')
                    ->label('Inspeção')
                    ->relationship('inspection', 'id')
                    ->required(),
                Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->default(null),
                Textarea::make('payload_json')
                    ->label('Dados (JSON)')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
