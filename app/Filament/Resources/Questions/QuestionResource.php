<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Pages\ViewQuestion;
use App\Filament\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Resources\Questions\Schemas\QuestionInfolist;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Model;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    public static function canEdit(Model $record): bool
    {
        /** @var Question $record */
        return !optional(optional(optional($record->category)->section)->questionnaireVersion)->inspections()->exists();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var Question $record */
        return !optional(optional(optional($record->category)->section)->questionnaireVersion)->inspections()->exists();
    }
    
    public static function canCreate(): bool
    {
        // One might want to block creation if there are no active versions, 
        // but that's not explicitly requested.
        return true;
    }

    protected static string|UnitEnum|null $navigationGroup = 'Configuração';

    protected static ?string $label = 'Pergunta';

    protected static ?string $pluralLabel = 'Perguntas';

    protected static ?string $navigationLabel = 'Perguntas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $recordTitleAttribute = 'text';

    public static function form(Schema $schema): Schema
    {
        return QuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
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
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'view' => ViewQuestion::route('/{record}'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}
