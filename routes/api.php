<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Tasks API routes
    Route::apiResource('tasks', TaskController::class);
    Route::post('/tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus']);
    Route::get('/tasks-stats', [TaskController::class, 'stats']);
    Route::get('/tasks-overdue', [TaskController::class, 'overdue']);

    // Categories API routes
    Route::apiResource('categories', CategoryController::class);


});
