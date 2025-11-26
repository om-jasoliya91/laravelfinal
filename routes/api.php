<?php

// use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'addAdmin']);
Route::post('/login', [RegisterController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout']);
    Route::post('/createUser', [RegisterController::class, 'createUser']);
    Route::get('/getUsers',[RegisterController::class, 'getUsers']);
});