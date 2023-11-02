<?php

use App\Http\Controllers\BattleshipController;
use App\Http\Controllers\RoomController;
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

Route::middleware('tokencheck')->group(function () {
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('room.index');
        Route::post('/store', [RoomController::class, 'store'])->name('room.store');
        Route::get('/{room}', [RoomController::class, 'show'])->name('room.show');
    });

    Route::prefix('rooms/{room}')->group(function () {
        Route::post('/leave', [RoomController::class, 'leave'])->name('room.leave');
        Route::post('/join', [RoomController::class, 'join'])->name('room.join');
        Route::post('/start', [RoomController::class, 'start'])->name('room.start');
        Route::delete('/restart', [RoomController::class, 'restart'])->name('room.restart');
        Route::post('/setBattleship', [BattleshipController::class, 'setBattleship'])->name('battleship.setBattleship');
        Route::post('/shoot', [BattleshipController::class, 'shoot'])->name('battleship.shoot');
    });
});