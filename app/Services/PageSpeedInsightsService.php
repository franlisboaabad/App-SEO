<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PageSpeedInsightsService
{
    /**
     * API Key de Google PageSpeed Insights
     * Nota: La API es gratuita pero tiene límites de cuota
     * Obtén tu API key en: https://developers.google.com/speed/docs/insights/v5/get-started
     */
    protected $apiKey;
    protected $apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    public function __construct()
    {
        // Obtener API key desde configuración
        $this->apiKey = config('services.pagespeed.api_key');
    }

    /**
     * Analizar URL con PageSpeed Insights
     *
     * @param string $url URL a analizar
     * @param string $strategy 'mobile' o 'desktop'
     * @return array|null Datos de PageSpeed Insights o null si hay error
     */
    public function analyzeUrl($url, $strategy = 'mobile')
    {
        if (empty($this->apiKey)) {
            Log::warning('PageSpeed Insights API key no configurada. Ve a config/services.php');
            return null;
        }

        try {
            // Aumentar timeout a 60 segundos para PageSpeed Insights
            $response = Http::timeout(60)->get($this->apiUrl, [
                'url' => $url,
                'key' => $this->apiKey,
                'strategy' => $strategy,
                'category' => ['performance'], // Solo performance para ser más rápido
            ]);

            if (!$response->successful()) {
                Log::error("Error en PageSpeed Insights API: " . $response->body());
                return null;
            }

            $data = $response->json();

            if (!isset($data['lighthouseResult'])) {
                Log::error("Respuesta inválida de PageSpeed Insights API");
                return null;
            }

            return $this->extractMetrics($data, $strategy);
        } catch (Exception $e) {
            Log::error("Excepción al llamar PageSpeed Insights API: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extraer métricas relevantes de la respuesta de PageSpeed Insights
     */
    protected function extractMetrics($data, $strategy)
    {
        $lighthouse = $data['lighthouseResult'];
        $audits = $lighthouse['audits'];
        $categories = $lighthouse['categories'];

        // Score general (Performance)
        $score = isset($categories['performance']['score'])
            ? round($categories['performance']['score'] * 100)
            : null;

        // Core Web Vitals
        $metrics = [
            'score' => $score,
            'fcp' => $this->getMetricValue($audits, 'first-contentful-paint'), // First Contentful Paint (ms)
            'lcp' => $this->getMetricValue($audits, 'largest-contentful-paint'), // Largest Contentful Paint (ms)
            'cls' => $this->getMetricValue($audits, 'cumulative-layout-shift'), // Cumulative Layout Shift (score)
            'fid' => $this->getMetricValue($audits, 'max-potential-fid'), // First Input Delay (ms) - usa max-potential-fid como aproximación
            'tti' => $this->getMetricValue($audits, 'interactive'), // Time to Interactive (ms)
        ];

        // Recomendaciones de optimización
        $recommendations = $this->extractRecommendations($audits);

        return [
            'metrics' => $metrics,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Obtener valor de una métrica específica
     */
    protected function getMetricValue($audits, $metricKey)
    {
        if (!isset($audits[$metricKey]) || !isset($audits[$metricKey]['numericValue'])) {
            return null;
        }

        $value = $audits[$metricKey]['numericValue'];

        // Convertir a segundos si es necesario (PageSpeed devuelve en milisegundos)
        if (in_array($metricKey, ['first-contentful-paint', 'largest-contentful-paint', 'interactive', 'max-potential-fid'])) {
            return round($value / 1000, 2); // Convertir ms a segundos
        }

        return $value;
    }

    /**
     * Extraer recomendaciones de optimización
     */
    protected function extractRecommendations($audits)
    {
        $recommendations = [];

        // Métricas críticas que necesitan optimización
        $criticalMetrics = [
            'render-blocking-resources' => 'Eliminar recursos que bloquean el renderizado',
            'unused-css-rules' => 'Eliminar CSS no utilizado',
            'unused-javascript' => 'Eliminar JavaScript no utilizado',
            'modern-image-formats' => 'Usar formatos de imagen modernos (WebP, AVIF)',
            'offscreen-images' => 'Optimizar imágenes fuera de la pantalla',
            'unminified-css' => 'Minificar CSS',
            'unminified-javascript' => 'Minificar JavaScript',
            'efficient-animated-content' => 'Optimizar animaciones',
            'uses-text-compression' => 'Habilitar compresión de texto (gzip/brotli)',
            'uses-optimized-images' => 'Optimizar imágenes',
            'uses-responsive-images' => 'Usar imágenes responsivas',
            'server-response-time' => 'Mejorar tiempo de respuesta del servidor',
            'uses-long-cache-ttl' => 'Configurar caché de largo plazo',
            'total-byte-weight' => 'Reducir tamaño total de la página',
        ];

        foreach ($criticalMetrics as $key => $title) {
            if (isset($audits[$key]) && isset($audits[$key]['score']) && $audits[$key]['score'] < 0.9) {
                $recommendations[] = [
                    'title' => $title,
                    'description' => $audits[$key]['description'] ?? '',
                    'score' => round($audits[$key]['score'] * 100),
                    'impact' => $audits[$key]['score'] < 0.5 ? 'high' : 'medium',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Analizar URL para mobile y desktop
     */
    public function analyzeBoth($url)
    {
        $mobile = $this->analyzeUrl($url, 'mobile');
        $desktop = $this->analyzeUrl($url, 'desktop');

        return [
            'mobile' => $mobile,
            'desktop' => $desktop,
        ];
    }
}

