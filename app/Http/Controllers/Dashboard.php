<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SeoMetric;
use App\Models\SeoAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Dashboard extends Controller
{
    public function home()
    {
        // Estadísticas generales
        $totalSites = Site::count();
        $activeSites = Site::where('estado', true)->count();
        $totalMetrics = SeoMetric::count();
        $totalAudits = SeoAudit::count();
        $completedAudits = SeoAudit::where('status', 'completed')->count();

        // Métricas de los últimos 7 días
        $metricsLast7Days = SeoMetric::where('date', '>=', Carbon::now()->subDays(7))
            ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->first();

        // Últimas auditorías
        $recentAudits = SeoAudit::with('site', 'result')
            ->latest()
            ->limit(5)
            ->get();

        // Sitios recientes
        $recentSites = Site::latest()
            ->limit(5)
            ->get();

        // Sitios con más métricas
        $topSitesByMetrics = Site::withCount('seoMetrics')
            ->orderByDesc('seo_metrics_count')
            ->limit(5)
            ->get();

        // Auditorías por estado
        $auditsByStatus = [
            'completed' => SeoAudit::where('status', 'completed')->count(),
            'pending' => SeoAudit::where('status', 'pending')->count(),
            'processing' => SeoAudit::where('status', 'processing')->count(),
            'failed' => SeoAudit::where('status', 'failed')->count(),
        ];

        return view('dashboard', compact(
            'totalSites',
            'activeSites',
            'totalMetrics',
            'totalAudits',
            'completedAudits',
            'metricsLast7Days',
            'recentAudits',
            'recentSites',
            'topSitesByMetrics',
            'auditsByStatus'
        ));
    }
}
