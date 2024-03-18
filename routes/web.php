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

Route::prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/create', [NoteController::class, 'create'])->name('notes.create');
    Route::post('/', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/{id}', [NoteController::class, 'show'])->name('notes.show');
    Route::get('/{id}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('/{id}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');
});

Route::prefix('notepad')->group(function () {
    Route::get('/', [NotePadController::class, 'index'])->name('notepad.index');
    Route::get('/create', [NotePadController::class, 'create'])->name('notepad.create');
    Route::post('/', [NotePadController::class, 'store'])->name('notepad.store');
    Route::get('/{id}', [NotePadController::class, 'show'])->name('notepad.show');
    Route::get('/{id}/edit', [NotePadController::class, 'edit'])->name('notepad.edit');
    Route::put('/{id}', [NotePadController::class, 'update'])->name('notepad.update');
    Route::delete('/{id}', [NotePadController::class, 'destroy'])->name('notepad.destroy');
    Route::put('/{id}/restore', [NotePadController::class, 'restore'])->name('notepad.restore');
    Route::post('/{id}/copy', [NotePadController::class, 'copy'])->name('notepad.copy');
});
