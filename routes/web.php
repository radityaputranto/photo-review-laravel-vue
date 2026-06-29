<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ─── Public ─────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth (bawaan Breeze, jangan hapus) ──────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Customer ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])
        ->name('dashboard');

    // Gallery & Selections
    Route::get('/sessions/{session}/gallery', [\App\Http\Controllers\Customer\GalleryController::class, 'show'])
        ->name('sessions.gallery');

    Route::post('/sessions/{session}/select', [\App\Http\Controllers\Customer\SelectionController::class, 'toggle'])
        ->name('sessions.select');

    Route::get('/sessions/{session}/review', [\App\Http\Controllers\Customer\SelectionController::class, 'review'])
        ->name('sessions.review');

    Route::post('/sessions/{session}/submit', [\App\Http\Controllers\Customer\SelectionController::class, 'submit'])
        ->name('sessions.submit');

    // Documents
    Route::get('/documents', [\App\Http\Controllers\Customer\DocumentController::class, 'index'])
        ->name('documents.index');

    Route::get('/documents/{document}/preview', [\App\Http\Controllers\Customer\DocumentController::class, 'preview'])
        ->name('documents.preview');
});

// ─── Admin & Photographer ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,photographer'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Sessions
    Route::resource('sessions', \App\Http\Controllers\Admin\SessionController::class);

    // Validate Drive URL (AJAX)
    Route::post('/sessions/validate-drive-url', [\App\Http\Controllers\Admin\SessionController::class, 'validateDriveUrl'])
        ->name('sessions.validate-drive-url');

    // Selections
    Route::get('/sessions/{session}/selections', [\App\Http\Controllers\Admin\SelectionController::class, 'show'])
        ->name('sessions.selections');

    Route::get('/sessions/{session}/selections/export', [\App\Http\Controllers\Admin\SelectionController::class, 'export'])
        ->name('sessions.selections.export');

    Route::delete('/sessions/{session}/selections/reset', [\App\Http\Controllers\Admin\SelectionController::class, 'reset'])
        ->name('sessions.selections.reset');

    // ─── Admin Only ─────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        // Documents
        Route::get('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'index'])
            ->name('documents.index');

        Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])
            ->name('documents.store');

        Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])
            ->name('documents.destroy');

        // Customers
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::patch('/customers/{customer}/toggle-active', [\App\Http\Controllers\Admin\CustomerController::class, 'toggleActive'])
            ->name('customers.toggle-active');
    });
});
