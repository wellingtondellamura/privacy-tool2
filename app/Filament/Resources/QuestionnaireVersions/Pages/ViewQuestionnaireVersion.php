<?php

namespace App\Filament\Resources\QuestionnaireVersions\Pages;

use App\Filament\Resources\QuestionnaireVersions\QuestionnaireVersionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionnaireVersion extends ViewRecord
{
    protected static string $resource = QuestionnaireVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
