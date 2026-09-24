<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HierarchyController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RevenueDataController;
use App\Http\Controllers\RegionalMapController;
use App\Http\Controllers\LevelKpiController;
use App\Http\Controllers\KelasKpiController;
use App\Http\Controllers\BudgetBK\IndirectChannelController;
use App\Http\Controllers\BudgetBK\DirectSalesController;
use App\Http\Controllers\BudgetBK\CultureProgramController;
use App\Http\Controllers\BackupController;

// Direct root route to Welcome page
Route::get('/', function () {
    return view('welcome-page.welcome');
})->name('welcome');


// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google OAuth Routes
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Read-only accessible for both USER (ADMIN) & VISITOR
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/regional-map', [RegionalMapController::class, 'index'])->name('regional-map.index');
    Route::get('/regional-map/markers', [RegionalMapController::class, 'getMarkersJson'])->name('regional-map.markers');
    Route::get('/regional-map/template', [RegionalMapController::class, 'downloadTemplate'])->name('regional-map.template');
    Route::get('/regional-map/export', [RegionalMapController::class, 'exportData'])->name('regional-map.export');

    Route::get('/hierarchy', [HierarchyController::class, 'index'])->name('hierarchy.index');
    Route::get('/hierarchy/template', [HierarchyController::class, 'downloadTemplate'])->name('hierarchy.template');
    Route::get('/hierarchy/export', [HierarchyController::class, 'exportExcel'])->name('hierarchy.export');

    Route::get('/revenue/manage', [RevenueDataController::class, 'index'])->name('revenue.manage');
    Route::get('/revenue/template', [RevenueDataController::class, 'downloadTemplate'])->name('revenue.template');
    Route::get('/revenue/export', [RevenueDataController::class, 'exportData'])->name('revenue.export');

    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');
    Route::get('/ranking/export', [RankingController::class, 'export'])->name('ranking.export');
    Route::get('/ranking/template', [RankingController::class, 'downloadTemplate'])->name('ranking.template');

    // Route::get('/level-kpi', [LevelKpiController::class, 'index'])->name('level-kpi.index');
    // Route::get('/level-kpi/export', [LevelKpiController::class, 'export'])->name('level-kpi.export');

    Route::get('/kelas-kpi', [KelasKpiController::class, 'index'])->name('kelas-kpi.index');
    Route::get('/kelas-kpi/export', [KelasKpiController::class, 'export'])->name('kelas-kpi.export');
    Route::get('/kelas-kpi/template', [KelasKpiController::class, 'downloadTemplate'])->name('kelas-kpi.template');

    Route::get('/growth-revenue/template', [DashboardController::class, 'downloadGrowthTemplate'])->name('growth.template');

    // MONITORING BUDGET BK Routes - Indirect Channel
    Route::get('/budget-bk/indirect-channel', [IndirectChannelController::class, 'index'])->name('budget-bk.indirect-channel.index');
    Route::get('/budget-bk/indirect-channel/export', [IndirectChannelController::class, 'export'])->name('budget-bk.indirect-channel.export');
    Route::post('/budget-bk/indirect-channel/import', [IndirectChannelController::class, 'import'])->name('budget-bk.indirect-channel.import');
    Route::post('/budget-bk/indirect-channel/expense', [IndirectChannelController::class, 'storeExpense'])->name('budget-bk.indirect-channel.expense.store');
    Route::post('/budget-bk/indirect-channel/expense/{id}/update', [IndirectChannelController::class, 'updateExpense'])->name('budget-bk.indirect-channel.expense.update');
    Route::post('/budget-bk/indirect-channel/expense/{id}/status', [IndirectChannelController::class, 'updateStatus'])->name('budget-bk.indirect-channel.expense.status');
    Route::delete('/budget-bk/indirect-channel/expense/{id}', [IndirectChannelController::class, 'destroyExpense'])->name('budget-bk.indirect-channel.expense.destroy');

    // MONITORING BUDGET BK Routes - Direct Sales
    Route::get('/budget-bk/direct-sales', [DirectSalesController::class, 'index'])->name('budget-bk.direct-sales.index');
    Route::get('/budget-bk/direct-sales/export', [DirectSalesController::class, 'export'])->name('budget-bk.direct-sales.export');
    Route::post('/budget-bk/direct-sales/import', [DirectSalesController::class, 'import'])->name('budget-bk.direct-sales.import');
    Route::post('/budget-bk/direct-sales/expense', [DirectSalesController::class, 'storeExpense'])->name('budget-bk.direct-sales.expense.store');
    Route::post('/budget-bk/direct-sales/expense/{id}/update', [DirectSalesController::class, 'updateExpense'])->name('budget-bk.direct-sales.expense.update');
    Route::post('/budget-bk/direct-sales/expense/{id}/status', [DirectSalesController::class, 'updateStatus'])->name('budget-bk.direct-sales.expense.status');
    Route::delete('/budget-bk/direct-sales/expense/{id}', [DirectSalesController::class, 'destroyExpense'])->name('budget-bk.direct-sales.expense.destroy');

    // MONITORING BUDGET BK Routes - Culture Program
    Route::get('/budget-bk/culture-program', [CultureProgramController::class, 'index'])->name('budget-bk.culture-program.index');
    Route::get('/budget-bk/culture-program/export', [CultureProgramController::class, 'export'])->name('budget-bk.culture-program.export');
    Route::post('/budget-bk/culture-program/import', [CultureProgramController::class, 'import'])->name('budget-bk.culture-program.import');
    Route::post('/budget-bk/culture-program/expense', [CultureProgramController::class, 'storeExpense'])->name('budget-bk.culture-program.expense.store');
    Route::post('/budget-bk/culture-program/expense/{id}/update', [CultureProgramController::class, 'updateExpense'])->name('budget-bk.culture-program.expense.update');
    Route::post('/budget-bk/culture-program/expense/{id}/status', [CultureProgramController::class, 'updateStatus'])->name('budget-bk.culture-program.expense.status');
    Route::delete('/budget-bk/culture-program/expense/{id}', [CultureProgramController::class, 'destroyExpense'])->name('budget-bk.culture-program.expense.destroy');

    // Mutation & Data Modification Routes: STRICTLY USER (ADMIN) ONLY
    Route::middleware('admin.only')->group(function () {
        Route::post('/ranking/import', [RankingController::class, 'import'])->name('ranking.import');
        Route::post('/kelas-kpi/import', [KelasKpiController::class, 'import'])->name('kelas-kpi.import');
        Route::post('/kelas-kpi', [KelasKpiController::class, 'store'])->name('kelas-kpi.store');
        Route::put('/kelas-kpi/{id}', [KelasKpiController::class, 'update'])->name('kelas-kpi.update');
        Route::delete('/kelas-kpi/{id}', [KelasKpiController::class, 'destroy'])->name('kelas-kpi.destroy');
        // Route::post('/level-kpi', [LevelKpiController::class, 'store'])->name('level-kpi.store');
        // Route::put('/level-kpi/{id}', [LevelKpiController::class, 'update'])->name('level-kpi.update');
        // Route::delete('/level-kpi/{id}', [LevelKpiController::class, 'destroy'])->name('level-kpi.destroy');
        Route::post('/regional-map/import', [RegionalMapController::class, 'import'])->name('regional-map.import');
        Route::post('/regional-map/manual', [RegionalMapController::class, 'storeManual'])->name('regional-map.manual');
        Route::put('/regional-map/{id}', [RegionalMapController::class, 'update'])->name('regional-map.update');
        Route::delete('/regional-map/{id}', [RegionalMapController::class, 'destroy'])->name('regional-map.destroy');
        Route::post('/regional-map/reset', [RegionalMapController::class, 'resetData'])->name('regional-map.reset');

        Route::put('/hierarchy/{id}', [HierarchyController::class, 'update'])->name('hierarchy.update');
        Route::post('/hierarchy/import', [HierarchyController::class, 'import'])->name('hierarchy.import');

        Route::post('/revenue/manual', [RevenueDataController::class, 'storeManual'])->name('revenue.manual');
        Route::put('/revenue/{id}', [RevenueDataController::class, 'update'])->name('revenue.update');
        Route::delete('/revenue/{id}', [RevenueDataController::class, 'destroy'])->name('revenue.destroy');
        Route::post('/revenue/import', [RevenueDataController::class, 'import'])->name('revenue.import');

        Route::post('/growth-revenue/import', [DashboardController::class, 'importGrowthData'])->name('growth.import');
        Route::post('/growth-revenue/reset', [DashboardController::class, 'resetGrowthData'])->name('growth.reset');

        // Backup & Restore Routes (Admin Only)
        Route::get('/admin/backups', [BackupController::class, 'index'])->name('admin.backups.index');
        Route::post('/admin/backups/create', [BackupController::class, 'create'])->name('admin.backups.create');
        Route::get('/admin/backups/download/{fileName}', [BackupController::class, 'download'])->name('admin.backups.download');
        Route::post('/admin/backups/restore', [BackupController::class, 'restore'])->name('admin.backups.restore');
        Route::delete('/admin/backups/{fileName}', [BackupController::class, 'destroy'])->name('admin.backups.destroy');
    });
});

