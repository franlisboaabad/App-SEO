<?php

namespace App\Services;

use App\Models\Site;
use App\Models\KeywordResearch;
use App\Models\SeoMetric;
use App\Models\Keyword;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeywordResearchService
{
    /**
     * Buscar keywords desde Google Search Console
     * Obtiene keywords que ya están rankeando pero no están siendo trackeadas
     */
    public function searchFromGSC(Site $site, $limit = 50)
    {
        if (!$site->gsc_property || !$site->gsc_credentials) {
            return [];
        }

        // Obtener keywords de GSC que no están en la tabla keywords
        $trackedKeywords = Keyword::where('site_id', $site->id)
            ->pluck('keyword')
            ->toArray();

        $keywords = SeoMetric::where('site_id', $site->id)
            ->whereNotNull('keyword')
            ->whereNotIn('keyword', $trackedKeywords)
            ->selectRaw('keyword, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position, AVG(ctr) as avg_ctr')
            ->groupBy('keyword')
            ->orderByDesc('total_clicks')
            ->limit($limit)
            ->get();

        $results = [];
        foreach ($keywords as $metric) {
            $research = KeywordResearch::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'keyword' => $metric->keyword,
                ],
                [
                    'current_position' => round($metric->avg_position, 1),
                    'clicks' => $metric->total_clicks,
                    'impressions' => $metric->total_impressions,
                    'ctr' => round($metric->avg_ctr, 2),
                    'source' => 'gsc',
                    'intent' => $this->detectIntent($metric->keyword),
                    'cluster' => $this->detectCluster($metric->keyword),
                ]
            );

            $results[] = $research;
        }

        return $results;
    }

    /**
     * Obtener sugerencias de Google Autocomplete
     */
    public function getAutocompleteSuggestions($query, $locale = 'es')
    {
        try {
            $url = "http://suggestqueries.google.com/complete/search";
            $params = [
                'client' => 'firefox',
                'q' => $query,
                'hl' => $locale,
            ];

            $response = Http::timeout(5)->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                // Google Autocomplete devuelve: [query, [suggestions], ...]
                return $data[1] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::warning("Error al obtener autocomplete para '{$query}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar keywords relacionadas usando autocomplete
     */
    public function searchRelatedKeywords(Site $site, $seedKeyword, $depth = 1)
    {
        $suggestions = $this->getAutocompleteSuggestions($seedKeyword);
        $results = [];

        foreach ($suggestions as $suggestion) {
            // Guardar sugerencia principal
            $research = KeywordResearch::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'keyword' => $suggestion,
                ],
                [
                    'source' => 'autocomplete',
                    'intent' => $this->detectIntent($suggestion),
                    'cluster' => $this->detectCluster($suggestion),
                    'notes' => "Sugerencia de: {$seedKeyword}",
                ]
            );

            $results[] = $research;

            // Si depth > 1, buscar sugerencias de las sugerencias
            if ($depth > 1) {
                $subSuggestions = $this->getAutocompleteSuggestions($suggestion);
                foreach ($subSuggestions as $subSuggestion) {
                    if ($subSuggestion !== $suggestion) {
                        KeywordResearch::firstOrCreate(
                            [
                                'site_id' => $site->id,
                                'keyword' => $subSuggestion,
                            ],
                            [
                                'source' => 'autocomplete',
                                'intent' => $this->detectIntent($subSuggestion),
                                'cluster' => $this->detectCluster($subSuggestion),
                                'notes' => "Sugerencia de: {$suggestion}",
                            ]
                        );
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Analizar keywords de competidores
     */
    public function analyzeCompetitorKeywords(Site $site, $competitorDomain)
    {
        // Esta funcionalidad requeriría scraping o API de herramientas externas
        // Por ahora, retornamos un array vacío
        // En el futuro se puede integrar con Ahrefs, SEMrush, etc.
        return [];
    }

    /**
     * Detectar intención de búsqueda basada en palabras clave (mejorado)
     */
    public function detectIntent($keyword)
    {
        $keywordLower = mb_strtolower($keyword, 'UTF-8');

        // Palabras transaccionales (alta intención de compra)
        $transactional = ['comprar', 'precio', 'costo', 'barato', 'oferta', 'descuento', 'venta', 'adquirir', 'pedir', 'ordenar', 'pagar', 'carrito', 'checkout', 'comprar online', 'comprar ahora'];
        foreach ($transactional as $word) {
            if (mb_strpos($keywordLower, $word, 0, 'UTF-8') !== false) {
                return 'transactional';
            }
        }

        // Palabras comerciales (intención de comparar/investigar antes de comprar)
        $commercial = ['mejor', 'comparar', 'vs', 'versus', 'revisión', 'review', 'opinión', 'comparativa', 'top', 'ranking', 'guía', 'guia', 'cuál', 'cual', 'qué', 'que'];
        foreach ($commercial as $word) {
            if (mb_strpos($keywordLower, $word, 0, 'UTF-8') !== false) {
                return 'commercial';
            }
        }

        // Palabras navegacionales (buscar sitio específico)
        $navigational = ['login', 'inicio', 'home', 'página', 'sitio', 'web', 'oficial', 'página web'];
        foreach ($navigational as $word) {
            if (mb_strpos($keywordLower, $word, 0, 'UTF-8') !== false) {
                return 'navigational';
            }
        }

        // Palabras informacionales (aprender, entender)
        $informational = ['qué es', 'que es', 'cómo', 'como', 'qué son', 'que son', 'definición', 'definicion', 'significado', 'qué significa', 'que significa', 'tutorial', 'aprender', 'información', 'informacion'];
        foreach ($informational as $word) {
            if (mb_strpos($keywordLower, $word, 0, 'UTF-8') !== false) {
                return 'informational';
            }
        }

        // Por defecto, informacional
        return 'informational';
    }

    /**
     * Agrupar keywords en clusters/temas
     */
    public function groupIntoClusters($keywords)
    {
        $clusters = [];

        foreach ($keywords as $keyword) {
            $cluster = $this->detectCluster($keyword);

            if (!isset($clusters[$cluster])) {
                $clusters[$cluster] = [];
            }

            $clusters[$cluster][] = $keyword;
        }

        return $clusters;
    }

    /**
     * Detectar cluster/tema de una keyword
     */
    private function detectCluster($keyword)
    {
        $keywordLower = mb_strtolower($keyword, 'UTF-8');

        // Extraer palabra principal (primera palabra significativa)
        $words = explode(' ', $keywordLower);
        $mainWord = '';

        $stopWords = ['el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'para', 'con', 'por', 'del', 'los', 'las'];

        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word, 'UTF-8') > 3 && !in_array($word, $stopWords)) {
                $mainWord = $word;
                break;
            }
        }

        // Si no encontramos palabra principal, usar la primera palabra
        if (empty($mainWord) && !empty($words)) {
            $mainWord = $words[0];
        }

        return ucfirst($mainWord ?: 'General');
    }

    /**
     * Obtener datos de Google Trends (básico, sin API oficial)
     * Nota: Google Trends no tiene API pública gratuita, esto es una aproximación
     */
    public function getTrendData($keyword)
    {
        // Google Trends no tiene API pública fácil
        // Esta función retorna un score estimado basado en análisis básico
        // Para datos reales, necesitarías usar la API no oficial o scraping

        // Por ahora, retornamos un score estimado
        $wordCount = str_word_count($keyword);
        $length = strlen($keyword);

        // Keywords más cortas y genéricas suelen tener más tendencia
        if ($wordCount == 1 && $length < 8) {
            return ['score' => rand(70, 100), 'trend' => 'up'];
        } elseif ($wordCount <= 2) {
            return ['score' => rand(40, 70), 'trend' => 'stable'];
        } else {
            return ['score' => rand(10, 40), 'trend' => 'down'];
        }
    }

    /**
     * Buscar keywords relacionadas con mejor intención
     * Filtra keywords con buena intención de búsqueda (comprar, comparar, aprender)
     */
    public function filterByGoodIntent($keywords)
    {
        $goodIntents = ['transactional', 'commercial', 'informational'];

        return array_filter($keywords, function($keyword) use ($goodIntents) {
            if (is_object($keyword)) {
                return in_array($keyword->intent, $goodIntents);
            }
            return isset($keyword['intent']) && in_array($keyword['intent'], $goodIntents);
        });
    }

    /**
     * Analizar keywords y asignar clusters automáticamente
     */
    public function assignClusters(Site $site)
    {
        $keywords = KeywordResearch::where('site_id', $site->id)
            ->whereNull('cluster')
            ->get();

        $updated = 0;

        foreach ($keywords as $keyword) {
            $cluster = $this->detectCluster($keyword->keyword);
            $keyword->update(['cluster' => $cluster]);
            $updated++;
        }

        return $updated;
    }

    /**
     * Estimar volumen de búsqueda (básico, sin API externa)
     * Esto es una estimación muy básica. Para datos reales necesitarías Google Keyword Planner API
     */
    public function estimateSearchVolume($keyword)
    {
        // Estimación muy básica basada en longitud y palabras comunes
        $length = strlen($keyword);
        $wordCount = str_word_count($keyword);

        // Keywords más cortas y con menos palabras suelen tener más volumen
        if ($wordCount == 1 && $length < 10) {
            return rand(10000, 100000); // Alto volumen estimado
        } elseif ($wordCount <= 2) {
            return rand(1000, 10000); // Volumen medio
        } else {
            return rand(100, 1000); // Bajo volumen
        }
    }

    /**
     * Estimar dificultad (básico, sin API externa)
     */
    public function estimateDifficulty($keyword, $currentPosition = null)
    {
        // Si ya está rankeando, la dificultad es menor
        if ($currentPosition && $currentPosition <= 20) {
            return rand(20, 50); // Dificultad media-baja
        }

        $wordCount = str_word_count($keyword);
        $length = strlen($keyword);

        // Keywords más cortas y genéricas son más difíciles
        if ($wordCount == 1 && $length < 8) {
            return rand(70, 100); // Muy difícil
        } elseif ($wordCount <= 2) {
            return rand(40, 70); // Media
        } else {
            return rand(10, 40); // Fácil
        }
    }

    /**
     * Agregar keyword al tracking
     */
    public function addToTracking(KeywordResearch $research, $targetUrl = null)
    {
        // Verificar que no exista ya
        $exists = Keyword::where('site_id', $research->site_id)
            ->where('keyword', $research->keyword)
            ->exists();

        if ($exists) {
            return false;
        }

        // Crear keyword en tracking
        Keyword::create([
            'site_id' => $research->site_id,
            'keyword' => $research->keyword,
            'target_url' => $targetUrl,
            'current_position' => $research->current_position,
            'notes' => "Agregada desde investigación (fuente: {$research->source})",
        ]);

        // Marcar como trackeada
        $research->update(['is_tracked' => true]);

        return true;
    }
}

