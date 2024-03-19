<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NotePadController;

use Illuminate\Support\Facades\Route;

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

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/', [TagController::class, 'store'])->name('tags.store');
    Route::get('/{id}', [TagController::class, 'show'])->name('tags.show');
    Route::get('/{id}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
});

Route::prefix('notepads')->group(function () {
    Route::get('/', [NotePadController::class, 'index'])->name('notepads.index');
    Route::post('/', [NotePadController::class, 'store'])->name('notepads.store');
    Route::get('/{id}', [NotePadController::class, 'show'])->name('notepads.show');
    Route::put('/{id}', [NotePadController::class, 'update'])->name('notepads.update');
    Route::delete('/{id}', [NotePadController::class, 'destroy'])->name('notepads.destroy');
    Route::put('/restore/{id}', [NotePadController::class, 'restore'])->name('notepads.restore');
    Route::post('/copy/{id}', [NotePadController::class, 'copy'])->name('notepads.copy');
});
