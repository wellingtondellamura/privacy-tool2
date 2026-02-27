<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Projeto')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('email')
                    ->label('Endereço de email')
                    ->email()
                    ->required(),
                TextInput::make('token')
                    ->label('Token')
                    ->required(),
                Select::make('role')
                    ->label('Função')
                    ->options(['evaluator' => 'Avaliador', 'observer' => 'Observador'])
                    ->default('evaluator')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->label('Expira em')
                    ->required(),
                DateTimePicker::make('accepted_at')
                    ->label('Aceito em'),
            ]);
    }
}
