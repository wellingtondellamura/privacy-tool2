<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\EvaluationRoundController;
use App\Http\Controllers\EvaluationRoundPublicationController;
use App\Http\Controllers\RoundBadgeController;
use App\Http\Controllers\PublicDirectoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/metodo-mitra', function () {
    return Inertia::render('MitraMethod');
})->name('metodo.mitra');

Route::get('/dashboard', function () {
    return redirect()->route('projects.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');

// Public Directory routes (spec.md 4.2)
Route::get('/tools', [PublicDirectoryController::class, 'index'])
    ->name('public.tools.index');
Route::get('/tools/{slug}', [PublicDirectoryController::class, 'show'])
    ->name('public.tools.show');

// Public Badge endpoints (spec.md 4)
Route::get('/badge/{token}', [RoundBadgeController::class, 'publicShow'])
    ->name('badge.show');
Route::get('/badge/{token}.js', [RoundBadgeController::class, 'publicScript'])
    ->name('badge.script');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Project routes (spec.md 7.1)
    Route::resource('projects', ProjectController::class);

    // Invitation routes (spec.md 7.2)
    Route::post('/projects/{project}/invite', [InvitationController::class, 'invite'])
        ->name('projects.invite');
    Route::post('/invitations/{invitation}/resend', [InvitationController::class, 'resend'])
        ->name('invitations.resend');
    Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])
        ->name('invitations.destroy');

    // Member routes
    Route::put('/projects/{project}/members/{user}', [ProjectController::class, 'updateMemberRole'])
        ->name('projects.members.update');

    // Inspection routes (spec.md 7.3)
    Route::post('/projects/{project}/inspections', [InspectionController::class, 'store'])
        ->name('inspections.store');
    Route::get('/inspections/{inspection}', [InspectionController::class, 'show'])
        ->name('inspections.show');
    Route::post('/inspections/{inspection}/activate', [InspectionController::class, 'activate'])
        ->name('inspections.activate');
    Route::post('/inspections/{inspection}/close', [InspectionController::class, 'close'])
        ->name('inspections.close');

    // Evaluation Round routes
    Route::post('/projects/{project}/rounds', [EvaluationRoundController::class, 'store'])
        ->name('projects.rounds.store');
    Route::get('/rounds/{round}', [EvaluationRoundController::class, 'show'])
        ->name('rounds.show');
    Route::post('/rounds/{round}/close', [EvaluationRoundController::class, 'close'])
        ->name('rounds.close');

    // Response routes (spec.md 7.3)
    Route::post('/inspections/{inspection}/response', [ResponseController::class, 'store'])
        ->name('responses.store');

    // Result routes (spec.md 7.4)
    Route::get('/inspections/{inspection}/results', [ResultController::class, 'individual'])
        ->name('results.individual');
    Route::get('/inspections/{inspection}/team-results', [ResultController::class, 'team'])
        ->name('results.team');
    Route::get('/inspections/{inspection}/comparison/{other}', [ResultController::class, 'comparison'])
        ->name('results.comparison');
    Route::get('/rounds/{round}/results', [ResultController::class, 'round'])
        ->name('rounds.results');
    Route::get('/rounds/{round}/review', [EvaluationRoundController::class, 'review'])
        ->name('rounds.review');
    Route::get('/rounds/{round}/comparison/{other}', [ResultController::class, 'roundComparison'])
        ->name('rounds.comparison');

    // Export routes
    Route::get('/projects/{project}/export', [DataExportController::class, 'exportProject'])
        ->name('projects.export');
    Route::get('/profile/export-all', [DataExportController::class, 'exportAll'])
        ->name('profile.export-all');

    // Round Publication routes
    Route::post('/rounds/{round}/publish', [EvaluationRoundPublicationController::class, 'store'])
        ->name('rounds.publish');
    Route::put('/rounds/{round}/publish', [EvaluationRoundPublicationController::class, 'update'])
        ->name('rounds.publications.update');
    Route::delete('/rounds/{round}/publish', [EvaluationRoundPublicationController::class, 'destroy'])
        ->name('rounds.publications.destroy');

    // Round Badge routes (spec.md 4)
    Route::post('/rounds/{round}/badge', [RoundBadgeController::class, 'store'])
        ->name('rounds.badge.store');
    Route::delete('/badges/{badge}', [RoundBadgeController::class, 'destroy'])
        ->name('badges.destroy');
    Route::put('/badges/{badge}/style', [RoundBadgeController::class, 'updateStyle'])
        ->name('badges.style.update');
});

require __DIR__.'/auth.php';
