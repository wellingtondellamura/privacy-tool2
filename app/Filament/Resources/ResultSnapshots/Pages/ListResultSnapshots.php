<?php

namespace App\Filament\Resources\ResultSnapshots\Pages;

use App\Filament\Resources\ResultSnapshots\ResultSnapshotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResultSnapshots extends ListRecords
{
    protected static string $resource = ResultSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
