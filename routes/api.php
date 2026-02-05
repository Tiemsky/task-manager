<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
*Auth Routes for API
*/

Route::post('/auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/profile', [\App\Http\Controllers\Api\AuthController::class, 'show']);

    // Tasks API routes
    Route::apiResource('tasks', TaskController::class);
    Route::post('/tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus']);
    Route::get('/tasks-stats', [TaskController::class, 'stats']);
    Route::get('/tasks-overdue', [TaskController::class, 'overdue']);

    // Categories API routes
    Route::apiResource('categories', CategoryController::class);

    /* Logout Route */
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

});
