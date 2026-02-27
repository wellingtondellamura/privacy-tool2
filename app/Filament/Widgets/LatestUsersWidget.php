<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Últimos Usuários Registrados';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado em')
                    ->dateTime(),
            ]);
    }
}
