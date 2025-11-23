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
     * Detectar intención de búsqueda basada en palabras clave
     */
    private function detectIntent($keyword)
    {
        $keywordLower = strtolower($keyword);

        // Palabras transaccionales
        $transactional = ['comprar', 'precio', 'costo', 'barato', 'oferta', 'descuento', 'venta', 'comprar', 'adquirir'];
        foreach ($transactional as $word) {
            if (strpos($keywordLower, $word) !== false) {
                return 'transactional';
            }
        }

        // Palabras comerciales
        $commercial = ['mejor', 'comparar', 'vs', 'versus', 'revisión', 'review', 'opinión'];
        foreach ($commercial as $word) {
            if (strpos($keywordLower, $word) !== false) {
                return 'commercial';
            }
        }

        // Palabras navegacionales
        $navigational = ['login', 'inicio', 'home', 'página', 'sitio', 'web'];
        foreach ($navigational as $word) {
            if (strpos($keywordLower, $word) !== false) {
                return 'navigational';
            }
        }

        // Por defecto, informacional
        return 'informational';
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

