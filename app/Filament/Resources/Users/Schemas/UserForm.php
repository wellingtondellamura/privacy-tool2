<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required(),
                TextInput::make('email')
                    ->label('Endereço de E-mail')
                    ->email()
                    ->required(),
                Toggle::make('is_admin')
                    ->label('Administrador')
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('E-mail verificado em'),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required(),
            ]);
    }
}
