<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvitationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('project.name')
                    ->numeric(),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('token'),
                TextEntry::make('role'),
                TextEntry::make('expires_at')
                    ->dateTime(),
                TextEntry::make('accepted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
