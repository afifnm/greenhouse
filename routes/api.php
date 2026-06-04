<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\Admin\GreenhouseController as AdminGreenhouseController;
use App\Http\Controllers\Api\Admin\MelonVarietyController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Greenhouse\FruitController;
use App\Http\Controllers\Api\Greenhouse\MaterialRequestController;
use App\Http\Controllers\Api\Greenhouse\ReportController;
use App\Http\Controllers\Api\Greenhouse\TreeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
| Auth: Laravel Sanctum bearer tokens
| Base URL: /api/v1
*/

Route::prefix('v1')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);

    // ── Authenticated ────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Dashboard (role-aware)
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Varieties list (used when creating sales / trees)
        Route::get('varieties', [MelonVarietyController::class, 'all']);

        // ── Admin only ───────────────────────────────────────────────────
        Route::middleware('role:admin')->prefix('admin')->group(function () {

            // Greenhouses
            Route::apiResource('greenhouses', AdminGreenhouseController::class);
            Route::get('greenhouses/{greenhouse}/managers', [AdminGreenhouseController::class, 'managers']);
            Route::put('greenhouses/{greenhouse}/managers', [AdminGreenhouseController::class, 'assignManagers']);

            // Users
            Route::apiResource('users', UserController::class);

            // Melon varieties
            Route::apiResource('varieties', MelonVarietyController::class);
        });

        // ── Sales ────────────────────────────────────────────────────────
        Route::prefix('sales')->group(function () {
            Route::get('/', [SaleController::class, 'index']);
            Route::get('{sale}', [SaleController::class, 'show']);
            Route::delete('{sale}', [SaleController::class, 'destroy']);

            // Per-greenhouse
            Route::get('greenhouse/{greenhouse}', [SaleController::class, 'greenhouse']);
            Route::post('greenhouse/{greenhouse}', [SaleController::class, 'store']);
            Route::get('greenhouse/{greenhouse}/report', [SaleController::class, 'report']);
        });

        // ── Greenhouse-scoped (trees, fruits, materials, reports) ────────
        Route::middleware('gh.access')->prefix('greenhouse/{greenhouse}')->group(function () {

            // Trees
            Route::apiResource('trees', TreeController::class);
            Route::post('trees/bulk', [TreeController::class, 'bulkCreate']);

            // Fruits (nested under trees)
            Route::apiResource('trees/{tree}/fruits', FruitController::class)
                ->except(['index']);
            Route::get('trees/{tree}/fruits', [FruitController::class, 'index']);

            // Material requests
            Route::get('material-requests', [MaterialRequestController::class, 'index']);
            Route::post('material-requests', [MaterialRequestController::class, 'store']);
            Route::patch('material-requests/{materialRequest}', [MaterialRequestController::class, 'update']);
            Route::delete('material-requests/{materialRequest}', [MaterialRequestController::class, 'destroy']);

            // Report
            Route::get('report', [ReportController::class, 'show']);
        });
    });
});
