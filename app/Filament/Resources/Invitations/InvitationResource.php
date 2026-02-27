<?php

namespace App\Filament\Resources\Invitations;

use App\Filament\Resources\Invitations\Pages\CreateInvitation;
use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\Invitations\Pages\ViewInvitation;
use App\Filament\Resources\Invitations\Schemas\InvitationForm;
use App\Filament\Resources\Invitations\Schemas\InvitationInfolist;
use App\Filament\Resources\Invitations\Tables\InvitationsTable;
use App\Models\Invitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|UnitEnum|null $navigationGroup = 'Gestão de Acesso';

    protected static ?string $label = 'Convite';

    protected static ?string $pluralLabel = 'Convites';

    protected static ?string $navigationLabel = 'Convites';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?string $recordTitleAttribute = 'email';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('accepted_at')->where('expires_at', '>=', now())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return InvitationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvitationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvitationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvitations::route('/'),
            'create' => CreateInvitation::route('/create'),
            'view' => ViewInvitation::route('/{record}'),
            'edit' => EditInvitation::route('/{record}/edit'),
        ];
    }
}
