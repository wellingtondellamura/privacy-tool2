<?php

namespace App\Filament\Resources\QuestionnaireVersions;

use App\Filament\Resources\QuestionnaireVersions\Pages\CreateQuestionnaireVersion;
use App\Filament\Resources\QuestionnaireVersions\Pages\EditQuestionnaireVersion;
use App\Filament\Resources\QuestionnaireVersions\Pages\ListQuestionnaireVersions;
use App\Filament\Resources\QuestionnaireVersions\Pages\ViewQuestionnaireVersion;
use App\Filament\Resources\QuestionnaireVersions\Schemas\QuestionnaireVersionForm;
use App\Filament\Resources\QuestionnaireVersions\Schemas\QuestionnaireVersionInfolist;
use App\Filament\Resources\QuestionnaireVersions\Tables\QuestionnaireVersionsTable;
use App\Models\QuestionnaireVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireVersionResource extends Resource
{
    protected static ?string $model = QuestionnaireVersion::class;

    public static function canEdit(Model $record): bool
    {
        /** @var QuestionnaireVersion $record */
        return !$record->inspections()->exists();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var QuestionnaireVersion $record */
        return !$record->inspections()->exists();
    }

    protected static string|UnitEnum|null $navigationGroup = 'Configuração';

    protected static ?string $label = 'Versão de Questionário';

    protected static ?string $pluralLabel = 'Versões de Questionário';

    protected static ?string $navigationLabel = 'Versões de Questionário';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'version_number';

    public static function form(Schema $schema): Schema
    {
        return QuestionnaireVersionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionnaireVersionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionnaireVersionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionnaireVersions::route('/'),
            'create' => CreateQuestionnaireVersion::route('/create'),
            'view' => ViewQuestionnaireVersion::route('/{record}'),
            'edit' => EditQuestionnaireVersion::route('/{record}/edit'),
        ];
    }
}
