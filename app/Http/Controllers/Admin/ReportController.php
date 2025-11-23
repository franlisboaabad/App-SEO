<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Models\SeoMetric;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.sites.show');
    }

    /**
     * Generar reporte PDF completo de un sitio
     */
    public function siteReport(Site $site, Request $request)
    {
        $days = (int) $request->get('days', 30);
        $endDate = Carbon::yesterday()->format('Y-m-d');
        $startDate = Carbon::parse($endDate)->subDays($days - 1)->format('Y-m-d');

        // Obtener métricas
        $metrics = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('DATE(date) as date, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->groupByRaw('DATE(date)')
            ->orderByRaw('DATE(date)')
            ->get();

        $summary = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->first();

        $topUrls = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('url, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->groupBy('url')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        $topKeywords = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('keyword, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->whereNotNull('keyword')
            ->groupBy('keyword')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        // Auditorías recientes
        $recentAudits = $site->seoAudits()
            ->with('result')
            ->completed()
            ->latest()
            ->limit(5)
            ->get();

        $data = [
            'site' => $site,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'days' => $days,
            'metrics' => $metrics,
            'summary' => $summary,
            'topUrls' => $topUrls,
            'topKeywords' => $topKeywords,
            'recentAudits' => $recentAudits,
        ];

        return $this->generatePDF('admin.reports.site-report', $data, "Reporte_Sitio_{$site->nombre}_{$endDate}.pdf");
    }

    /**
     * Generar reporte PDF de una auditoría
     */
    public function auditReport(SeoAudit $audit)
    {
        $audit->load('result', 'site');

        $data = [
            'audit' => $audit,
        ];

        return $this->generatePDF('admin.reports.audit-report', $data, "Auditoria_{$audit->id}_{$audit->created_at->format('Y-m-d')}.pdf");
    }

    /**
     * Generar reporte PDF de métricas
     */
    public function metricsReport(Site $site, Request $request)
    {
        $days = (int) $request->get('days', 30);
        $endDate = Carbon::yesterday()->format('Y-m-d');
        $startDate = Carbon::parse($endDate)->subDays($days - 1)->format('Y-m-d');

        $metrics = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('DATE(date) as date, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->groupByRaw('DATE(date)')
            ->orderByRaw('DATE(date)')
            ->get();

        $summary = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->first();

        $data = [
            'site' => $site,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => $metrics,
            'summary' => $summary,
        ];

        return $this->generatePDF('admin.reports.metrics-report', $data, "Metricas_{$site->nombre}_{$endDate}.pdf");
    }

    /**
     * Generar PDF genérico
     */
    private function generatePDF($view, $data, $filename)
    {
        $html = View::make($view, $data)->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->stream($filename);
    }
}
