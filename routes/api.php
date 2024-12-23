<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GameTypeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('games', GameController::class);

Route::apiResource('players', PlayerController::class);

Route::apiResource('teams', TeamController::class);

Route::apiResource('game-types', GameTypeController::class);
