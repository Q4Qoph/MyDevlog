<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ClientViewController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

// ── Public routes (no auth) ───────────────────────────────────────────────────

Route::get('/share/{token}', [ClientViewController::class, 'show'])->name('client.show');

Route::get('/approve/{token}',      [ApprovalController::class, 'show'])  ->name('approval.show');
Route::post('/approve/{token}',     [ApprovalController::class, 'decide'])->name('approval.decide');
Route::get('/approve/{token}/done', [ApprovalController::class, 'done'])  ->name('approval.done');


// ── Authenticated routes ──────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (replaces Breeze's default)
    Route::get('/', [ProjectController::class, 'index'])->name('dashboard');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/share',       [ProjectController::class, 'toggleShare'])           ->name('projects.share');
    Route::post('projects/{project}/suggestions', [ProjectController::class, 'regenerateSuggestions']) ->name('projects.suggestions.regenerate');

    // Features
    Route::post('projects/{project}/features',     [FeatureController::class, 'store'])            ->name('features.store');
    Route::patch('features/{feature}',             [FeatureController::class, 'update'])           ->name('features.update');
    Route::patch('features/{feature}/status',      [FeatureController::class, 'updateStatus'])     ->name('features.status');
    Route::post('features/{feature}/send-approval',[FeatureController::class, 'sendApproval'])     ->name('features.approval.send');
    Route::delete('features/{feature}',            [FeatureController::class, 'destroy'])          ->name('features.destroy');

    // AI suggestions
    Route::post('projects/{project}/suggestions/accept',  [FeatureController::class, 'acceptSuggestion']) ->name('suggestions.accept');
    Route::post('projects/{project}/suggestions/dismiss', [FeatureController::class, 'dismissSuggestion'])->name('suggestions.dismiss');

    // Profile (Breeze standard — keep these)
    Route::get('/profile',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';