<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\SeoAuditController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\KeywordController;
use App\Http\Controllers\Admin\SeoTaskController;
use App\Http\Controllers\Admin\CompetitorController;
use App\Http\Controllers\Admin\UserManualController;
use App\Http\Controllers\Admin\SeoAlertController;
use App\Http\Controllers\Admin\KeywordResearchController;
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

    // Validación técnica
    Route::post('sites/{site}/validate-technical', [SiteController::class, 'validateTechnical'])->name('sites.validate-technical');

    // Exportar métricas
    Route::get('sites/{site}/export-metrics', [SiteController::class, 'exportMetrics'])->name('sites.export-metrics');

    // Rutas de auditorías SEO
    Route::post('sites/{site}/audit', [SeoAuditController::class, 'runAudit'])->name('sites.audit');
    Route::get('sites/{site}/audits', [SeoAuditController::class, 'index'])->name('sites.audits');
    Route::get('audits/{audit}', [SeoAuditController::class, 'show'])->name('audits.show');
    Route::get('audits/{audit}/export-internal-links', [SeoAuditController::class, 'exportInternalLinks'])->name('audits.export-internal-links');
    Route::get('audits/{audit}/export-external-links', [SeoAuditController::class, 'exportExternalLinks'])->name('audits.export-external-links');
    Route::get('audits/{audit}/export-broken-links', [SeoAuditController::class, 'exportBrokenLinks'])->name('audits.export-broken-links');
    Route::get('audits/export/results', [SeoAuditController::class, 'exportResults'])->name('audits.export-results');

    // Rutas de reportes PDF
    Route::get('sites/{site}/report', [ReportController::class, 'siteReport'])->name('sites.report');
    Route::get('sites/{site}/metrics-report', [ReportController::class, 'metricsReport'])->name('sites.metrics-report');
    Route::get('audits/{audit}/report', [ReportController::class, 'auditReport'])->name('audits.report');

    // Rutas de Keywords
    Route::resource('keywords', KeywordController::class);
    Route::get('keywords/{keyword}/dashboard', [KeywordController::class, 'dashboard'])->name('keywords.dashboard');
    Route::post('keywords/update-positions', [KeywordController::class, 'updatePositions'])->name('keywords.update-positions');
    Route::get('keywords/export/excel', [KeywordController::class, 'export'])->name('keywords.export');

    // Rutas de Investigación de Keywords
    Route::get('keyword-research', [KeywordResearchController::class, 'index'])->name('keyword-research.index');
    Route::get('keyword-research/clusters', [KeywordResearchController::class, 'clusters'])->name('keyword-research.clusters');
    Route::post('keyword-research/search-gsc', [KeywordResearchController::class, 'searchFromGSC'])->name('keyword-research.search-gsc');
    Route::post('keyword-research/search-related', [KeywordResearchController::class, 'searchRelated'])->name('keyword-research.search-related');
    Route::post('keyword-research/{site}/assign-clusters', [KeywordResearchController::class, 'assignClusters'])->name('keyword-research.assign-clusters');
    Route::post('keyword-research/{site}/analyze-intent', [KeywordResearchController::class, 'analyzeIntent'])->name('keyword-research.analyze-intent');
    Route::post('keyword-research/{keywordResearch}/add-to-tracking', [KeywordResearchController::class, 'addToTracking'])->name('keyword-research.add-to-tracking');
    Route::put('keyword-research/{keywordResearch}', [KeywordResearchController::class, 'update'])->name('keyword-research.update');
    Route::delete('keyword-research/{keywordResearch}', [KeywordResearchController::class, 'destroy'])->name('keyword-research.destroy');
    Route::get('keyword-research/export/excel', [KeywordResearchController::class, 'export'])->name('keyword-research.export');

    // Rutas de Tareas SEO
    Route::resource('tasks', SeoTaskController::class);
    Route::get('tasks-kanban', [SeoTaskController::class, 'kanban'])->name('tasks.kanban');
    Route::post('tasks/{task}/update-status', [SeoTaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Rutas de Competidores
    Route::resource('competitors', CompetitorController::class);
    Route::get('sites/{site}/competitors/dashboard', [CompetitorController::class, 'dashboard'])->name('competitors.dashboard');
    Route::post('competitors/{competitor}/compare-keywords', [CompetitorController::class, 'compareKeywords'])->name('competitors.compare-keywords');
    Route::post('competitors/{competitor}/update-position', [CompetitorController::class, 'updatePosition'])->name('competitors.update-position');
    Route::post('competitors/{competitor}/update-positions', [CompetitorController::class, 'updatePositions'])->name('competitors.update-positions');

    // Manual de Usuario
    Route::get('user-manual', [UserManualController::class, 'index'])->name('user-manual.index');

    // Rutas de Alertas SEO
    Route::get('alerts', [SeoAlertController::class, 'index'])->name('alerts.index');
    Route::post('alerts/{alert}/mark-as-read', [SeoAlertController::class, 'markAsRead'])->name('alerts.mark-as-read');
    Route::post('alerts/{alert}/mark-as-resolved', [SeoAlertController::class, 'markAsResolved'])->name('alerts.mark-as-resolved');
    Route::post('alerts/mark-all-as-read', [SeoAlertController::class, 'markAllAsRead'])->name('alerts.mark-all-as-read');
    Route::post('alerts/detect-changes', [SeoAlertController::class, 'detectChanges'])->name('alerts.detect-changes');
    Route::get('alerts/unread-count', [SeoAlertController::class, 'getUnreadCount'])->name('alerts.unread-count');
});


require __DIR__.'/auth.php';
