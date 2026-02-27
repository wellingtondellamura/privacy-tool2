<?php

namespace App\Filament\Resources\ResultSnapshots\Pages;

use App\Filament\Resources\ResultSnapshots\ResultSnapshotResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditResultSnapshot extends EditRecord
{
    protected static string $resource = ResultSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
