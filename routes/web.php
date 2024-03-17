<?php

use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotePadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Get notepad detail info.
 */
Route::get('/notepad/{noteId}', [NotePadController::class, 'getDetail']);

/**
 * Update notepad info.
 */
Route::post('/notepad/{noteId}', [NotePadController::class, 'update']);

/**
 * Delete notepad logical.
 */
Route::delete('/notepad/{noteId}', [NotePadController::class, 'delete']);

/**
 * Create notepad info.
 */
Route::post('/notepad/', [NotePadController::class, 'create']);

/**
 * copy a notepad.
 */
Route::post('/copy/{noteId}', [NotePadController::class, 'copy']);

/**
 * restore a notepad.
 */
Route::post('/restore/{noteId}', [NotePadController::class, 'restore']);

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/', [TagController::class, 'store'])->name('tags.store');
    Route::get('/{id}', [TagController::class, 'show'])->name('tags.show');
    Route::get('/{id}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
});
