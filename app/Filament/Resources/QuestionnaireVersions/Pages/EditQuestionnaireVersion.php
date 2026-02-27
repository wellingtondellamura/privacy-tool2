<?php

namespace App\Filament\Resources\QuestionnaireVersions\Pages;

use App\Filament\Resources\QuestionnaireVersions\QuestionnaireVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestionnaireVersion extends EditRecord
{
    protected static string $resource = QuestionnaireVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
