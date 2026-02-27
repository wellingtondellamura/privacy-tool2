<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Filament\Notifications\Notification;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Endereço de e-mail')
                    ->searchable(),
                ToggleColumn::make('is_admin')
                    ->label('Administrador')
                    ->disabled(fn (User $record): bool => $record->is_admin && User::where('is_admin', true)->count() <= 1),
                IconColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null),
                TextColumn::make('email_verified_at')
                    ->label('E-mail verificado em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label('Administrador'),
                TernaryFilter::make('email_verified')
                    ->label('Email verificado')
                    ->placeholder('Todos')
                    ->trueLabel('Verificados')
                    ->falseLabel('Não verificados')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('email_verified_at'),
                        false: fn ($query) => $query->whereNull('email_verified_at'),
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Resetar senha')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                        ]);

                        Notification::make()
                            ->title('Senha resetada com sucesso!')
                            ->body('A nova senha é: password123')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
