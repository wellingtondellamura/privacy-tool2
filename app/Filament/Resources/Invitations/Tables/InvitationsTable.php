<?php

namespace App\Filament\Resources\Invitations\Tables;

use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Projeto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Endereço de Email')
                    ->searchable(),
                TextColumn::make('token')
                    ->label('Token')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Função'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Invitation $record): string => match (true) {
                        $record->accepted_at !== null => 'Aceito',
                        $record->expires_at->isPast() => 'Expirado',
                        default => 'Pendente',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Aceito' => 'success',
                        'Expirado' => 'danger',
                        'Pendente' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('expires_at')
                    ->label('Expira em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accepted_at')
                    ->label('Aceito em')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'accepted' => 'Aceito',
                        'expired' => 'Expirado',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'pending' => $query->whereNull('accepted_at')->where('expires_at', '>=', now()),
                            'accepted' => $query->whereNotNull('accepted_at'),
                            'expired' => $query->whereNull('accepted_at')->where('expires_at', '<', now()),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('invalidate')
                    ->label('Invalidar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Invitation $record): bool => $record->accepted_at === null && $record->expires_at->isFuture())
                    ->action(fn (Invitation $record) => $record->update(['expires_at' => now()])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
