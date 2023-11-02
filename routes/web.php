<?php

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

Route::group(['prefix' => 'rooms', 'middleware' => 'tokencheck'], function () {
    Route::get('/', [RoomController::class, 'index'])->name('room.index');
    Route::post('/store', [RoomController::class, 'store'])->name('room.store');
    Route::get('/{room}', [RoomController::class, 'show'])->name('room.show');
    Route::post('/{room}/leave', [RoomController::class, 'leave'])->name('room.leave');
    Route::post('/{room}/join', [RoomController::class, 'join'])->name('room.join');
});