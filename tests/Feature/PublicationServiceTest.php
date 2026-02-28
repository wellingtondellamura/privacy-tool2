<?php

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\User;
use App\Services\PublicationService;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Publication Service Tests
|--------------------------------------------------------------------------
| Validates Phase 3 - PublicationService logic
*/

beforeEach(function () {
    $this->service = new PublicationService();
    $this->user = User::factory()->create();
    $this->inspection = Inspection::factory()->closed()->create();
});

test('service can publish a closed inspection', function () {
    $publication = $this->service->publish($this->inspection, Visibility::SCORE_PUBLIC, $this->user);

    expect($publication)->toBeInstanceOf(InspectionPublication::class);
    expect($publication->visibility)->toBe(Visibility::SCORE_PUBLIC);
    expect($publication->inspection_id)->toBe($this->inspection->id);
    expect($publication->published_at)->not->toBeNull();
    expect($publication->published_by)->toBe($this->user->id);
    
    $this->assertDatabaseHas('inspection_publications', [
        'inspection_id' => $this->inspection->id,
        'visibility' => 'score_public',
    ]);
});

test('service throws exception when publishing non-closed inspection', function () {
    $activeInspection = Inspection::factory()->active()->create();
    
    expect(fn() => $this->service->publish($activeInspection, Visibility::SCORE_PUBLIC, $this->user))
        ->toThrow(\InvalidArgumentException::class);
});

test('service can update visibility', function () {
    $publication = $this->service->publish($this->inspection, Visibility::SCORE_PUBLIC, $this->user);
    
    $updated = $this->service->updateVisibility($this->inspection, Visibility::FULL_PUBLIC);
    
    expect($updated->visibility)->toBe(Visibility::FULL_PUBLIC);
    $this->assertDatabaseHas('inspection_publications', [
        'inspection_id' => $this->inspection->id,
        'visibility' => 'full_public',
    ]);
});

test('service can revoke publication', function () {
    $this->service->publish($this->inspection, Visibility::SCORE_PUBLIC, $this->user);
    
    $this->service->revoke($this->inspection);
    
    expect($this->inspection->fresh()->publication->visibility)->toBe(Visibility::PRIVATE);
});

test('publishing does not alter existing snapshot', function () {
    $snapshot = $this->inspection->resultSnapshots()->create([
        'user_id' => null, // consolidated
        'payload_json' => ['score' => 85],
    ]);
    
    $this->service->publish($this->inspection, Visibility::FULL_PUBLIC, $this->user);
    
    expect($snapshot->fresh()->payload_json)->toBe(['score' => 85]);
});
