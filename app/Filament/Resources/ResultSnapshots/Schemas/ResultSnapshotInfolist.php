<?php

namespace App\Filament\Resources\ResultSnapshots\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ResultSnapshotInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('inspection.id')
                    ->label('ID da Inspeção'),
                TextEntry::make('user.name')
                    ->label('Avaliador')
                    ->placeholder('Consolidado (Equipe)')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'success'),
                TextEntry::make('payload_json')
                    ->label('Dados Brutos (JSON)')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->prose()
                    ->columnSpanFull()
                    ->fontFamily('mono')
                    ->markdown(),
                TextEntry::make('created_at')
                    ->label('Gerado em')
                    ->dateTime(),
            ]);
    }
}
