<?php

namespace App\Filament\Resources\Inspections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InspectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sequential_id')
                    ->label('#'),
                TextEntry::make('project.name')
                    ->numeric(),
                TextEntry::make('user.name')
                    ->numeric(),
                TextEntry::make('questionnaireVersion.id')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('started_at')
                    ->dateTime(),
                TextEntry::make('closed_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
