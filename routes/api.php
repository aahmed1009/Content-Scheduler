<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PlatformController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::apiResource('posts', PostController::class)->only([
    'index', 'store', 'update', 'destroy'
]);


Route::get('/platforms', [PlatformController::class, 'index']);
Route::post('/platforms/toggle', [PlatformController::class, 'toggle']);
Route::get('/logs', function () {
    return auth()->user()->activityLogs()->latest()->get();
});