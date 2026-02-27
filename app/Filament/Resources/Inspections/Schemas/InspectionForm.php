<?php

namespace App\Filament\Resources\Inspections\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class InspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Projeto')
                    ->relationship('project', 'name')
                    ->required(),
                Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->default(null),
                Select::make('questionnaire_version_id')
                    ->label('Versão do Questionário')
                    ->relationship('questionnaireVersion', 'id')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(['draft' => 'Rascunho', 'active' => 'Ativo', 'closed' => 'Fechado'])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('started_at')
                    ->label('Iniciado em'),
                DateTimePicker::make('closed_at')
                    ->label('Fechado em'),
            ]);
    }
}
