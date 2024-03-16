<?php

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
 * This Router use to route HTTP request to get notepad detail info. 
 */
Route::get('/notepad/{noteId}', [NotePadController::class, 'getDetail']);

/**
 * This Router use to route HTTP request to update notepad info. 
 */
Route::post('/notepad/{noteId}', [NotePadController::class, 'update']);

/**
 * This Router use to route HTTP request to delete notepad logical. 
 */
Route::delete('/notepad/{noteId}', [NotePadController::class, 'delete']);