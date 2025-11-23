<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\SeoAuditController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Dashboard;
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

Route::get('/dashboard',[Dashboard::class,'home'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Rutas de administración SEO
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('usuarios', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('sites', SiteController::class);

    // Dashboard SEO
    Route::get('sites/{site}/dashboard', [SiteController::class, 'dashboard'])->name('sites.dashboard');

    // Sincronizar métricas
    Route::post('sites/{site}/sync-metrics', [SiteController::class, 'syncMetrics'])->name('sites.sync-metrics');

    // Rutas de auditorías SEO
    Route::post('sites/{site}/audit', [SeoAuditController::class, 'runAudit'])->name('sites.audit');
    Route::get('sites/{site}/audits', [SeoAuditController::class, 'index'])->name('sites.audits');
    Route::get('audits/{audit}', [SeoAuditController::class, 'show'])->name('audits.show');

    // Rutas de reportes PDF
    Route::get('sites/{site}/report', [ReportController::class, 'siteReport'])->name('sites.report');
    Route::get('sites/{site}/metrics-report', [ReportController::class, 'metricsReport'])->name('sites.metrics-report');
    Route::get('audits/{audit}/report', [ReportController::class, 'auditReport'])->name('audits.report');
});


require __DIR__.'/auth.php';
