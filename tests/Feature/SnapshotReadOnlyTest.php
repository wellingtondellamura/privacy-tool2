<?php

use App\Models\ResultSnapshot;
use App\Models\User;
use App\Filament\Resources\ResultSnapshots\ResultSnapshotResource;

test('result snapshot resource is read-only', function () {
    expect(ResultSnapshotResource::canCreate())->toBeFalse();
});

test('result snapshot cannot be edited or deleted', function () {
    $snapshot = ResultSnapshot::factory()->create();

    expect(ResultSnapshotResource::canEdit($snapshot))->toBeFalse();
    expect(ResultSnapshotResource::canDelete($snapshot))->toBeFalse();
});
