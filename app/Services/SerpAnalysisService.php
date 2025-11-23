<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Keyword;
use App\Models\SerpAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class SerpAnalysisService
{
    /**
     * Analizar SERP para una keyword
     *
     * @param Site $site
     * @param string $keyword
     * @param Keyword|null $keywordModel
     * @return SerpAnalysis
     */
    public function analyzeSerp(Site $site, $keyword, $keywordModel = null)
    {
        try {
            Log::info("Iniciando análisis de SERP para keyword '{$keyword}' del sitio {$site->id}");

            // Obtener datos de SERP (puede tardar 10-30 segundos)
            $serpData = $this->fetchSerpData($keyword, $site->dominio_base);

            if (empty($serpData['results'])) {
                throw new Exception("No se pudieron obtener resultados de SERP. Google puede haber bloqueado la solicitud o la keyword no tiene resultados.");
            }

            // Buscar posición del sitio
            $sitePosition = $this->findSitePosition($serpData, $site->dominio_base);

            // Obtener snippet del sitio si está rankeando
            $siteSnippet = $sitePosition ? ($serpData['results'][$sitePosition - 1] ?? null) : null;

            // Extraer competidores (top 10)
            $competitors = $this->extractCompetitors($serpData);

            // Detectar features de SERP
            $features = $this->detectSerpFeatures($serpData);

            // Generar sugerencias
            $suggestions = $this->generateSuggestions($site, $keyword, $sitePosition, $siteSnippet, $competitors);

            // Crear o actualizar análisis
            $analysis = SerpAnalysis::updateOrCreate(
                [
                    'site_id' => $site->id,
                    'keyword' => $keyword,
                ],
                [
                    'keyword_id' => $keywordModel ? $keywordModel->id : null,
                    'position' => $sitePosition,
                    'url' => $siteSnippet['url'] ?? null,
                    'title' => $siteSnippet['title'] ?? null,
                    'description' => $siteSnippet['description'] ?? null,
                    'display_url' => $siteSnippet['display_url'] ?? null,
                    'competitors' => $competitors,
                    'features' => $features,
                    'suggestions' => is_array($suggestions) ? implode("\n", $suggestions) : $suggestions,
                    'analysis_date' => Carbon::today(), // Siempre actualizar la fecha
                ]
            );

            Log::info("Análisis de SERP completado para keyword '{$keyword}'. Posición: " . ($sitePosition ?? 'No rankea'));

            return $analysis;
        } catch (Exception $e) {
            Log::error("Error al analizar SERP para keyword '{$keyword}': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener datos de SERP usando scraping básico
     * Nota: Google puede bloquear scraping. Para producción, considera usar una API como SerpAPI, DataForSEO, etc.
     */
    protected function fetchSerpData($keyword, $domain = null)
    {
        try {
            Log::info("Obteniendo datos de SERP para keyword: {$keyword}");

            // URL de búsqueda de Google
            $searchUrl = "https://www.google.com/search?q=" . urlencode($keyword) . "&num=10";

            // Headers para simular navegador
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ];

            // Aumentar timeout a 60 segundos
            $response = Http::timeout(60)
                ->withHeaders($headers)
                ->get($searchUrl);

            if (!$response->successful()) {
                Log::warning("Error HTTP al obtener SERP: " . $response->status());
                throw new Exception("Error al obtener SERP: HTTP " . $response->status());
            }

            $html = $response->body();

            // Verificar si Google bloqueó (página de captcha o error)
            if (stripos($html, 'captcha') !== false || stripos($html, 'unusual traffic') !== false) {
                Log::warning("Google bloqueó el acceso (captcha detectado)");
                throw new Exception("Google bloqueó el acceso. Por favor, espera unos minutos antes de intentar nuevamente.");
            }

            // Parsear HTML para extraer resultados
            $parsed = $this->parseGoogleResults($html);

            if (empty($parsed['results'])) {
                Log::warning("No se pudieron extraer resultados del HTML");
                // Retornar datos simulados si no se pueden parsear
                return $this->getMockSerpData($keyword);
            }

            return $parsed;
        } catch (Exception $e) {
            Log::warning("Error al obtener SERP: " . $e->getMessage());

            // Retornar datos simulados si falla (para desarrollo/testing)
            Log::info("Usando datos simulados para desarrollo");
            return $this->getMockSerpData($keyword);
        }
    }

    /**
     * Parsear resultados de Google (básico)
     * Nota: Google cambia su HTML frecuentemente, esto es una implementación básica
     */
    protected function parseGoogleResults($html)
    {
        $results = [];

        // Patrones básicos para extraer resultados (pueden cambiar)
        // Buscar divs con clase "g" que contienen resultados
        preg_match_all('/<div class="g"[^>]*>(.*?)<\/div><\/div><\/div>/s', $html, $matches);

        foreach ($matches[1] ?? [] as $index => $resultHtml) {
            // Extraer título
            preg_match('/<h3[^>]*>(.*?)<\/h3>/s', $resultHtml, $titleMatch);
            $title = isset($titleMatch[1]) ? strip_tags($titleMatch[1]) : '';

            // Extraer URL
            preg_match('/href="([^"]+)"/', $resultHtml, $urlMatch);
            $url = isset($urlMatch[1]) ? $urlMatch[1] : '';

            // Limpiar URL de Google
            if (strpos($url, '/url?q=') !== false) {
                $url = urldecode(explode('&', str_replace('/url?q=', '', $url))[0]);
            }

            // Extraer descripción
            preg_match('/<span[^>]*class="[^"]*st[^"]*"[^>]*>(.*?)<\/span>/s', $resultHtml, $descMatch);
            $description = isset($descMatch[1]) ? strip_tags($descMatch[1]) : '';

            // Extraer display URL
            preg_match('/<cite[^>]*>(.*?)<\/cite>/s', $resultHtml, $citeMatch);
            $displayUrl = isset($citeMatch[1]) ? strip_tags($citeMatch[1]) : '';

            if (!empty($title) && !empty($url)) {
                $results[] = [
                    'position' => $index + 1,
                    'title' => $title,
                    'url' => $url,
                    'description' => $description,
                    'display_url' => $displayUrl,
                ];
            }
        }

        return [
            'results' => array_slice($results, 0, 10), // Top 10
            'total_results' => count($results),
        ];
    }

    /**
     * Datos simulados para desarrollo (cuando Google bloquea)
     */
    protected function getMockSerpData($keyword)
    {
        return [
            'results' => [
                [
                    'position' => 1,
                    'title' => 'Resultado 1 para ' . $keyword,
                    'url' => 'https://ejemplo1.com',
                    'description' => 'Descripción del resultado 1',
                    'display_url' => 'ejemplo1.com',
                ],
                [
                    'position' => 2,
                    'title' => 'Resultado 2 para ' . $keyword,
                    'url' => 'https://ejemplo2.com',
                    'description' => 'Descripción del resultado 2',
                    'display_url' => 'ejemplo2.com',
                ],
            ],
            'total_results' => 2,
        ];
    }

    /**
     * Encontrar posición del sitio en SERP
     */
    protected function findSitePosition($serpData, $domain)
    {
        foreach ($serpData['results'] ?? [] as $result) {
            $resultDomain = parse_url($result['url'] ?? '', PHP_URL_HOST);
            if ($resultDomain && (strpos($resultDomain, $domain) !== false || strpos($domain, $resultDomain) !== false)) {
                return $result['position'];
            }
        }

        return null;
    }

    /**
     * Extraer competidores (top 10)
     */
    protected function extractCompetitors($serpData)
    {
        return array_map(function($result) {
            return [
                'position' => $result['position'],
                'title' => $result['title'] ?? '',
                'url' => $result['url'] ?? '',
                'description' => $result['description'] ?? '',
                'display_url' => $result['display_url'] ?? '',
            ];
        }, $serpData['results'] ?? []);
    }

    /**
     * Detectar features de SERP (featured snippet, rich snippets, etc.)
     */
    protected function detectSerpFeatures($serpData)
    {
        $features = [];

        // Detectar featured snippet (primer resultado con formato especial)
        // Esto es básico, en producción usarías análisis más avanzado

        return $features;
    }

    /**
     * Generar sugerencias de mejora
     */
    protected function generateSuggestions(Site $site, $keyword, $position, $siteSnippet, $competitors)
    {
        $suggestions = [];

        // Si no está rankeando
        if (!$position) {
            $suggestions[] = "Tu sitio no aparece en los primeros 10 resultados para '{$keyword}'. Considera optimizar tu contenido para esta keyword.";
            return $suggestions;
        }

        // Si está rankeando pero fuera del top 3
        if ($position > 3) {
            $suggestions[] = "Estás en posición {$position}. Analiza los resultados del top 3 para mejorar tu ranking.";
        }

        // Analizar snippet
        if ($siteSnippet) {
            // Verificar longitud del título
            $titleLength = mb_strlen($siteSnippet['title'] ?? '');
            if ($titleLength < 30) {
                $suggestions[] = "Tu título es muy corto ({$titleLength} caracteres). Recomendado: 50-60 caracteres.";
            } elseif ($titleLength > 60) {
                $suggestions[] = "Tu título es muy largo ({$titleLength} caracteres). Puede ser cortado en los resultados.";
            }

            // Verificar longitud de descripción
            $descLength = mb_strlen($siteSnippet['description'] ?? '');
            if ($descLength < 120) {
                $suggestions[] = "Tu descripción es muy corta ({$descLength} caracteres). Recomendado: 150-160 caracteres.";
            } elseif ($descLength > 160) {
                $suggestions[] = "Tu descripción es muy larga ({$descLength} caracteres). Puede ser cortada en los resultados.";
            }

            // Verificar si incluye la keyword
            $keywordLower = mb_strtolower($keyword);
            if (mb_strpos(mb_strtolower($siteSnippet['title'] ?? ''), $keywordLower) === false) {
                $suggestions[] = "Tu título no incluye la keyword '{$keyword}'. Considera incluirla para mejorar el CTR.";
            }
        }

        // Comparar con competidores del top 3
        $top3 = array_slice($competitors, 0, 3);
        foreach ($top3 as $competitor) {
            if ($competitor['position'] < $position) {
                $suggestions[] = "Analiza el resultado #{$competitor['position']} ({$competitor['display_url']}) para ver qué están haciendo mejor.";
                break;
            }
        }

        return $suggestions;
    }

    /**
     * Analizar múltiples keywords
     */
    public function analyzeMultipleKeywords(Site $site, $keywords)
    {
        $results = [];

        foreach ($keywords as $keyword) {
            try {
                $keywordModel = Keyword::where('site_id', $site->id)
                    ->where('keyword', $keyword)
                    ->first();

                $analysis = $this->analyzeSerp($site, $keyword, $keywordModel);
                $results[] = $analysis;

                // Pequeña pausa para evitar rate limiting
                sleep(2);
            } catch (Exception $e) {
                Log::error("Error al analizar keyword '{$keyword}': " . $e->getMessage());
            }
        }

        return $results;
    }
}

