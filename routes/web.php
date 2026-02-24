<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ResultController;
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

Route::get('/dashboard', function () {
    return redirect()->route('projects.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public: Accept invitation via token
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');

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
});

require __DIR__.'/auth.php';
