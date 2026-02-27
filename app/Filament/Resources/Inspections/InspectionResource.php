<?php

namespace App\Filament\Resources\Inspections;

use App\Filament\Resources\Inspections\Pages\CreateInspection;
use App\Filament\Resources\Inspections\Pages\EditInspection;
use App\Filament\Resources\Inspections\Pages\ListInspections;
use App\Filament\Resources\Inspections\Pages\ViewInspection;
use App\Filament\Resources\Inspections\Schemas\InspectionForm;
use App\Filament\Resources\Inspections\Schemas\InspectionInfolist;
use App\Filament\Resources\Inspections\Tables\InspectionsTable;
use App\Models\Inspection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\Inspection $record */
        return $record->status !== 'closed';
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\Inspection $record */
        return $record->status !== 'closed';
    }

    protected static string|UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $label = 'Inspeção';

    protected static ?string $pluralLabel = 'Inspeções';

    protected static ?string $navigationLabel = 'Inspeções';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function form(Schema $schema): Schema
    {
        return InspectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InspectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InspectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInspections::route('/'),
            'create' => CreateInspection::route('/create'),
            'view' => ViewInspection::route('/{record}'),
            'edit' => EditInspection::route('/{record}/edit'),
        ];
    }
}
