<?php

namespace App\Filament\Resources\QuestionnaireVersions\Pages;

use App\Filament\Resources\QuestionnaireVersions\QuestionnaireVersionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionnaireVersions extends ListRecords
{
    protected static string $resource = QuestionnaireVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
