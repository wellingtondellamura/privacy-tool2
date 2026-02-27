<?php

namespace App\Filament\Resources\ResultSnapshots\Pages;

use App\Filament\Resources\ResultSnapshots\ResultSnapshotResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResultSnapshot extends ViewRecord
{
    protected static string $resource = ResultSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
