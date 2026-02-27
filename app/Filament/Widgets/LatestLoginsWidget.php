<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLoginsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Últimos Logins';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->whereNotNull('last_login_at')->orderBy('last_login_at', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Usuário'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último Login')
                    ->dateTime()
                    ->description(fn (User $record): string => $record->last_login_at->diffForHumans()),
            ]);
    }
}
