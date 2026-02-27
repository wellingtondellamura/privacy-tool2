<?php

namespace App\Filament\Resources\Inspections\Tables;

use App\Models\Inspection;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class InspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sequential_id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label('Projeto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('questionnaireVersion.id')
                    ->label('Versão Questionário')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('started_at')
                    ->label('Início')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Término')
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
                    ->options([
                        'draft' => 'Rascunho',
                        'active' => 'Ativa',
                        'closed' => 'Fechada',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('forceClose')
                    ->label('Forçar fechamento')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Inspection $record): bool => $record->status === 'active')
                    ->action(function (Inspection $record) {
                        $record->transitionTo('closed');
                        Notification::make()
                            ->title('Inspeção fechada com sucesso')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
