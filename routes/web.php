<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\GreenhouseController as AdminGreenhouseController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MelonVarietyController;
use App\Http\Controllers\Greenhouse\TreeController;
use App\Http\Controllers\Greenhouse\FruitController;
use App\Http\Controllers\Greenhouse\MaterialRequestController;
use App\Http\Controllers\Greenhouse\ReportController;
use App\Http\Controllers\SaleController;
use App\Models\Greenhouse;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Auth
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('greenhouses', AdminGreenhouseController::class)->except(['show']);
        Route::get('greenhouses/{greenhouse}', [AdminGreenhouseController::class, 'show'])->name('greenhouses.show');
        Route::get('greenhouses/{greenhouse}/managers', [AdminGreenhouseController::class, 'managers'])->name('greenhouses.managers');
        Route::put('greenhouses/{greenhouse}/managers', [AdminGreenhouseController::class, 'assignManagers'])->name('greenhouses.managers.assign');

        Route::resource('users', AdminUserController::class);

        Route::resource('varieties', MelonVarietyController::class);
    });

    // Sales (kasir) — admin only
    Route::middleware(['role:admin'])->prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
    });

    // Manager routes - Greenhouse management
    Route::middleware(['gh.access'])->group(function () {
        Route::get('/greenhouse/{greenhouse}', function (Greenhouse $greenhouse) {
            $greenhouse->loadCount(['trees', 'trees as alive_trees_count' => fn($q) => $q->where('status', 'alive')]);
            return view('greenhouse.show', compact('greenhouse'));
        })->name('greenhouse.show');

        Route::get('/greenhouse/{greenhouse}/report', [ReportController::class, 'show'])->name('greenhouse.report');

        Route::get('/greenhouse/{greenhouse}/trees', [TreeController::class, 'index'])->name('greenhouse.trees.index');
        Route::get('/greenhouse/{greenhouse}/trees/create', [TreeController::class, 'create'])->name('greenhouse.trees.create');
        Route::post('/greenhouse/{greenhouse}/trees', [TreeController::class, 'store'])->name('greenhouse.trees.store');
        Route::get('/greenhouse/{greenhouse}/trees/bulk', [TreeController::class, 'bulkCreate'])->name('greenhouse.trees.bulk');
        Route::post('/greenhouse/{greenhouse}/trees/bulk', [TreeController::class, 'bulkCreate'])->name('greenhouse.trees.bulk.store');
        Route::get('/greenhouse/{greenhouse}/trees/{tree}', [TreeController::class, 'show'])->name('greenhouse.trees.show');
        Route::get('/greenhouse/{greenhouse}/trees/{tree}/edit', [TreeController::class, 'edit'])->name('greenhouse.trees.edit');
        Route::put('/greenhouse/{greenhouse}/trees/{tree}', [TreeController::class, 'update'])->name('greenhouse.trees.update');
        Route::delete('/greenhouse/{greenhouse}/trees/{tree}', [TreeController::class, 'destroy'])->name('greenhouse.trees.destroy');

        // Material Requests
        Route::get('/greenhouse/{greenhouse}/material-requests', [MaterialRequestController::class, 'index'])->name('greenhouse.material-requests.index');
        Route::post('/greenhouse/{greenhouse}/material-requests', [MaterialRequestController::class, 'store'])->name('greenhouse.material-requests.store');
        Route::patch('/greenhouse/{greenhouse}/material-requests/{materialRequest}', [MaterialRequestController::class, 'update'])->name('greenhouse.material-requests.update');
        Route::delete('/greenhouse/{greenhouse}/material-requests/{materialRequest}', [MaterialRequestController::class, 'destroy'])->name('greenhouse.material-requests.destroy');

        Route::get('/greenhouse/{greenhouse}/trees/{tree}/fruits', [FruitController::class, 'index'])->name('greenhouse.fruits.index');
        Route::get('/greenhouse/{greenhouse}/trees/{tree}/fruits/create', [FruitController::class, 'create'])->name('greenhouse.fruits.create');
        Route::post('/greenhouse/{greenhouse}/trees/{tree}/fruits', [FruitController::class, 'store'])->name('greenhouse.fruits.store');
        Route::get('/greenhouse/{greenhouse}/trees/{tree}/fruits/{fruit}/edit', [FruitController::class, 'edit'])->name('greenhouse.fruits.edit');
        Route::put('/greenhouse/{greenhouse}/trees/{tree}/fruits/{fruit}', [FruitController::class, 'update'])->name('greenhouse.fruits.update');
        Route::delete('/greenhouse/{greenhouse}/trees/{tree}/fruits/{fruit}', [FruitController::class, 'destroy'])->name('greenhouse.fruits.destroy');
    });

    Route::get('/', fn() => redirect()->route('dashboard'));
});
