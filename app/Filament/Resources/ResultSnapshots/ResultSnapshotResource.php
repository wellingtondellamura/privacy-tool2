<?php

namespace App\Filament\Resources\ResultSnapshots;

use App\Filament\Resources\ResultSnapshots\Pages\CreateResultSnapshot;
use App\Filament\Resources\ResultSnapshots\Pages\EditResultSnapshot;
use App\Filament\Resources\ResultSnapshots\Pages\ListResultSnapshots;
use App\Filament\Resources\ResultSnapshots\Pages\ViewResultSnapshot;
use App\Filament\Resources\ResultSnapshots\Schemas\ResultSnapshotForm;
use App\Filament\Resources\ResultSnapshots\Schemas\ResultSnapshotInfolist;
use App\Filament\Resources\ResultSnapshots\Tables\ResultSnapshotsTable;
use App\Models\ResultSnapshot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Model;

class ResultSnapshotResource extends Resource
{
    protected static ?string $model = ResultSnapshot::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    protected static string|UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $label = 'Snapshot de Resultado';

    protected static ?string $pluralLabel = 'Snapshots de Resultados';

    protected static ?string $navigationLabel = 'Snapshots de Resultados';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ResultSnapshotForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ResultSnapshotInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultSnapshotsTable::configure($table);
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
            'index' => ListResultSnapshots::route('/'),
            'view' => ViewResultSnapshot::route('/{record}'),
        ];
    }
}
