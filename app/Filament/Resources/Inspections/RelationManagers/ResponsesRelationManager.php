<?php

namespace App\Filament\Resources\Inspections\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Respostas';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.text')
                    ->label('Pergunta')
                    ->wrap(),
                TextColumn::make('answer')
                    ->label('Resposta'),
                TextColumn::make('observation')
                    ->label('Observação')
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Avaliador'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only from admin perspective usually
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
