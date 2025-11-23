<?php

namespace App\Services;

use App\Models\SeoAlert;
use App\Models\Site;
use App\Models\Keyword;
use App\Models\SeoMetric;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AlertService
{
    /**
     * Crear alerta de posición
     */
    public function createPositionAlert(Site $site, $keyword, $url, $oldPosition, $newPosition)
    {
        $change = $newPosition - $oldPosition;

        // Solo alertar si la pérdida es significativa (más de 5 posiciones)
        if ($change <= 5) {
            return;
        }

        $severity = $this->calculatePositionSeverity($oldPosition, $newPosition);

        SeoAlert::create([
            'site_id' => $site->id,
            'type' => 'position',
            'severity' => $severity,
            'title' => "Pérdida de posición: {$keyword}",
            'message' => "La keyword '{$keyword}' bajó de posición {$oldPosition} a {$newPosition} ({$change} posiciones)",
            'url' => $url,
            'keyword' => $keyword,
            'metadata' => [
                'old_position' => $oldPosition,
                'new_position' => $newPosition,
                'change' => $change,
            ],
        ]);
    }

    /**
     * Crear alerta de tráfico
     */
    public function createTrafficAlert(Site $site, $url, $oldClicks, $newClicks, $period = '7 días')
    {
        if ($oldClicks == 0) {
            return;
        }

        $changePercent = (($newClicks - $oldClicks) / $oldClicks) * 100;

        // Solo alertar si la caída es mayor al 20%
        if ($changePercent >= -20) {
            return;
        }

        $severity = abs($changePercent) > 50 ? 'critical' : 'warning';

        SeoAlert::create([
            'site_id' => $site->id,
            'type' => 'traffic',
            'severity' => $severity,
            'title' => "Caída de tráfico en {$url}",
            'message' => "Los clics bajaron un " . round(abs($changePercent), 1) . "% en los últimos {$period} (de {$oldClicks} a {$newClicks})",
            'url' => $url,
            'metadata' => [
                'old_clicks' => $oldClicks,
                'new_clicks' => $newClicks,
                'change_percent' => round($changePercent, 1),
                'period' => $period,
            ],
        ]);
    }

    /**
     * Crear alerta de error SEO
     */
    public function createErrorAlert(Site $site, $url, $errorType, $message)
    {
        SeoAlert::create([
            'site_id' => $site->id,
            'type' => 'error',
            'severity' => 'critical',
            'title' => "Error SEO: {$errorType}",
            'message' => $message,
            'url' => $url,
            'metadata' => [
                'error_type' => $errorType,
            ],
        ]);
    }

    /**
     * Crear alerta de contenido
     */
    public function createContentAlert(Site $site, $url, $issue, $message)
    {
        SeoAlert::create([
            'site_id' => $site->id,
            'type' => 'content',
            'severity' => 'warning',
            'title' => "Problema de contenido: {$issue}",
            'message' => $message,
            'url' => $url,
            'metadata' => [
                'issue' => $issue,
            ],
        ]);
    }

    /**
     * Detectar cambios de posición automáticamente
     */
    public function detectPositionChanges(Site $site = null)
    {
        $sites = $site ? collect([$site]) : Site::active()->get();
        $alertsCreated = 0;

        foreach ($sites as $site) {
            $keywords = Keyword::where('site_id', $site->id)
                ->whereNotNull('current_position')
                ->whereNotNull('previous_position')
                ->get();

            foreach ($keywords as $keyword) {
                $change = $keyword->current_position - $keyword->previous_position;

                // Solo alertar si hay pérdida significativa
                if ($change > 5) {
                    $this->createPositionAlert(
                        $site,
                        $keyword->keyword,
                        $keyword->target_url,
                        $keyword->previous_position,
                        $keyword->current_position
                    );
                    $alertsCreated++;
                }
            }
        }

        return $alertsCreated;
    }

    /**
     * Detectar caídas de tráfico
     */
    public function detectTrafficDrops(Site $site = null, $days = 7)
    {
        $sites = $site ? collect([$site]) : Site::active()->get();
        $alertsCreated = 0;

        foreach ($sites as $site) {
            $endDate = Carbon::yesterday();
            $startDate = $endDate->copy()->subDays($days);

            // Obtener métricas del período anterior
            $previousPeriod = SeoMetric::forSite($site->id)
                ->dateRange($startDate->copy()->subDays($days), $startDate->copy()->subDay())
                ->selectRaw('url, SUM(clicks) as total_clicks')
                ->groupBy('url')
                ->pluck('total_clicks', 'url');

            // Obtener métricas del período actual
            $currentPeriod = SeoMetric::forSite($site->id)
                ->dateRange($startDate, $endDate)
                ->selectRaw('url, SUM(clicks) as total_clicks')
                ->groupBy('url')
                ->pluck('total_clicks', 'url');

            foreach ($previousPeriod as $url => $oldClicks) {
                $newClicks = $currentPeriod[$url] ?? 0;

                if ($oldClicks > 0) {
                    $this->createTrafficAlert($site, $url, $oldClicks, $newClicks, "{$days} días");
                    $alertsCreated++;
                }
            }
        }

        return $alertsCreated;
    }

    /**
     * Calcular severidad basada en cambio de posición
     */
    private function calculatePositionSeverity($oldPosition, $newPosition)
    {
        $change = $newPosition - $oldPosition;

        if ($change > 20) {
            return 'critical';
        } elseif ($change > 10) {
            return 'warning';
        } else {
            return 'info';
        }
    }
}

