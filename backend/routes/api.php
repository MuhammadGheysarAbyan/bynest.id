<?php

use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PortfolioController;
use Illuminate\Support\Facades\Route;

// ===== Publik (tanpa login) =====
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/portfolios', [PortfolioController::class, 'index']);

// Login dibatasi 5 percobaan per menit
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// ===== Admin (wajib login) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('admin')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::apiResource('articles', AdminArticleController::class);
    });
});
