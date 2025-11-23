<?php

namespace App\Services;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Models\AuditResult;
use App\Models\SeoTask;
use App\Services\PageSpeedInsightsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class SeoAuditService
{
    /**
     * Ejecutar auditoría SEO para una URL
     * @param bool $checkBrokenLinks Si es true, verifica links rotos (puede ser lento)
     */
    public function auditUrl(Site $site, $url, $checkBrokenLinks = false)
    {
        try {
            // Normalizar URL
            $url = $this->normalizeUrl($url, $site->dominio_base);

            // Crear registro de auditoría
            $audit = SeoAudit::create([
                'site_id' => $site->id,
                'url' => $url,
                'status' => 'processing',
            ]);

            try {
                // Obtener contenido de la página
                $startTime = microtime(true);

                try {
                    // Desactivar verificación SSL para desarrollo (evita error cURL 60)
                    $response = Http::timeout(30)
                        ->withoutVerifying()
                        ->get($url);
                } catch (\Exception $httpException) {
                    // Capturar y formatear mejor el mensaje de error
                    $errorMsg = $httpException->getMessage();

                    // Si es error SSL, dar mensaje más claro
                    if (strpos($errorMsg, 'SSL') !== false || strpos($errorMsg, 'certificate') !== false) {
                        throw new Exception("Error SSL: No se pudo verificar el certificado. Esto puede ocurrir en desarrollo local.");
                    }

                    // Si es timeout
                    if (strpos($errorMsg, 'timeout') !== false) {
                        throw new Exception("Timeout: La página tardó más de 30 segundos en responder.");
                    }

                    // Error genérico de conexión
                    throw new Exception("Error de conexión: " . $errorMsg);
                }

                $endTime = microtime(true);
                $ttfb = $endTime - $startTime;

                $statusCode = $response->status();

                if ($statusCode >= 400) {
                    throw new Exception("HTTP Error {$statusCode}: " . ($response->body() ?: 'Sin respuesta del servidor'));
                }

                $html = $response->body();

                if (empty($html)) {
                    throw new Exception("La página no devolvió contenido HTML");
                }

                // Analizar HTML
                try {
                    $crawler = new Crawler($html);
                    $result = $this->analyzePage($crawler, $url, $ttfb, $statusCode, $checkBrokenLinks);
                } catch (\Exception $crawlerException) {
                    throw new Exception("Error al analizar el HTML: " . $crawlerException->getMessage());
                }

                // Analizar con PageSpeed Insights (opcional, puede fallar sin afectar la auditoría)
                // IMPORTANTE: Solo analizar mobile para evitar timeouts (desktop es opcional y más lento)
                try {
                    $pagespeedService = new PageSpeedInsightsService();

                    // Solo analizar mobile (más rápido, más importante para SEO móvil-first)
                    // Desktop se puede agregar después si es necesario
                    $mobile = $pagespeedService->analyzeUrl($url, 'mobile');

                    if ($mobile && isset($mobile['metrics'])) {
                        $result['pagespeed_score_mobile'] = $mobile['metrics']['score'] ?? null;
                        $result['fcp_mobile'] = $mobile['metrics']['fcp'] ?? null;
                        $result['lcp_mobile'] = $mobile['metrics']['lcp'] ?? null;
                        $result['cls_mobile'] = $mobile['metrics']['cls'] ?? null;
                        $result['fid_mobile'] = $mobile['metrics']['fid'] ?? null;
                        $result['tti_mobile'] = $mobile['metrics']['tti'] ?? null;

                        // Recomendaciones
                        if (isset($mobile['recommendations'])) {
                            $result['pagespeed_recommendations'] = $mobile['recommendations'];
                        }
                    }

                    // Desktop se puede agregar después en un job separado si es necesario
                    // Por ahora solo mobile para evitar timeouts
                } catch (\Exception $pagespeedException) {
                    // No fallar la auditoría si PageSpeed Insights falla o tarda mucho
                    Log::warning("Error al obtener datos de PageSpeed Insights para {$url}: " . $pagespeedException->getMessage());
                }

                // Guardar resultados
                $auditResult = AuditResult::create([
                    'seo_audit_id' => $audit->id,
                    ...$result,
                ]);

                // Actualizar estado de la auditoría
                $audit->update(['status' => 'completed']);

                // Generar alertas de contenido si hay problemas
                $this->generateContentAlerts($audit, $auditResult);

                // Generar tareas automáticamente desde errores y advertencias críticas
                $this->generateTasksFromAudit($audit, $auditResult);

                return $audit;
            } catch (Exception $e) {
                $audit->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                throw $e;
            }
        } catch (Exception $e) {
            Log::error("Error en auditoría SEO para {$url}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analizar página HTML
     * @param bool $checkBrokenLinks Si es true, verifica links rotos (puede ser lento)
     */
    private function analyzePage(Crawler $crawler, $url, $ttfb, $statusCode, $checkBrokenLinks = false)
    {
        $result = [
            'title' => null,
            'meta_description' => null,
            'h1_count' => 0,
            'h2_count' => 0,
            'h3_count' => 0,
            'images_without_alt' => 0,
            'images_total' => 0,
            'canonical' => null,
            'robots_meta' => null,
            'internal_links_count' => 0,
            'external_links_count' => 0,
            'broken_links_count' => 0,
            'ttfb' => $ttfb,
            'status_code' => $statusCode,
            'errors' => [],
            'warnings' => [],
        ];

        $baseUrl = parse_url($url, PHP_URL_HOST);

        // Title
        try {
            $titleNode = $crawler->filter('title')->first();
            if ($titleNode->count() > 0) {
                $result['title'] = trim($titleNode->text());
                if (strlen($result['title']) > 60) {
                    $result['warnings'][] = 'El título tiene más de 60 caracteres';
                }
                if (strlen($result['title']) < 30) {
                    $result['warnings'][] = 'El título tiene menos de 30 caracteres';
                }
            } else {
                $result['errors'][] = 'No se encontró el tag <title>';
            }
        } catch (Exception $e) {
            $result['errors'][] = 'Error al obtener el título: ' . $e->getMessage();
        }

        // Meta description
        try {
            $metaDescNode = $crawler->filter('meta[name="description"]')->first();
            if ($metaDescNode->count() > 0) {
                $result['meta_description'] = trim($metaDescNode->attr('content') ?? '');
                if (strlen($result['meta_description']) > 160) {
                    $result['warnings'][] = 'La meta descripción tiene más de 160 caracteres';
                }
                if (strlen($result['meta_description']) < 120) {
                    $result['warnings'][] = 'La meta descripción tiene menos de 120 caracteres';
                }
            } else {
                $result['warnings'][] = 'No se encontró la meta descripción';
            }
        } catch (Exception $e) {
            // Ignorar si no existe
        }

        // Headings
        try {
            $result['h1_count'] = $crawler->filter('h1')->count();
            $result['h2_count'] = $crawler->filter('h2')->count();
            $result['h3_count'] = $crawler->filter('h3')->count();

            if ($result['h1_count'] == 0) {
                $result['errors'][] = 'No se encontró ningún H1';
            } elseif ($result['h1_count'] > 1) {
                $result['warnings'][] = "Se encontraron {$result['h1_count']} H1 (debería haber solo uno)";
            }
        } catch (Exception $e) {
            // Ignorar
        }

        // Imágenes
        try {
            $images = $crawler->filter('img');
            $result['images_total'] = $images->count();

            $images->each(function (Crawler $img) use (&$result) {
                $alt = $img->attr('alt');
                if (empty($alt) || trim($alt) === '') {
                    $result['images_without_alt']++;
                }
            });

            if ($result['images_without_alt'] > 0) {
                $result['warnings'][] = "{$result['images_without_alt']} imágenes sin atributo ALT";
            }
        } catch (Exception $e) {
            // Ignorar
        }

        // Canonical
        try {
            $canonicalNode = $crawler->filter('link[rel="canonical"]')->first();
            if ($canonicalNode->count() > 0) {
                $result['canonical'] = $canonicalNode->attr('href');
            }
        } catch (Exception $e) {
            // Ignorar
        }

        // Robots meta
        try {
            $robotsNode = $crawler->filter('meta[name="robots"]')->first();
            if ($robotsNode->count() > 0) {
                $result['robots_meta'] = $robotsNode->attr('content');
            }
        } catch (Exception $e) {
            // Ignorar
        }

        // Análisis de contenido
        try {
            // Obtener texto del body (sin scripts y styles)
            $bodyText = $crawler->filter('body')->each(function (Crawler $node) {
                // Remover scripts, styles y otros elementos no visibles
                $node->filter('script, style, noscript')->each(function (Crawler $el) {
                    $el->getNode(0)->parentNode->removeChild($el->getNode(0));
                });
                return $node->text();
            });

            $fullText = implode(' ', $bodyText);
            $wordCount = str_word_count(strip_tags($fullText));
            $result['word_count'] = $wordCount;

            // Análisis de densidad de keywords (extraer palabras más frecuentes)
            // Limpiar y normalizar texto a UTF-8
            $fullText = mb_convert_encoding($fullText, 'UTF-8', 'UTF-8');
            $fullText = mb_convert_case($fullText, MB_CASE_LOWER, 'UTF-8');

            // Extraer palabras usando regex para mejor soporte UTF-8
            preg_match_all('/\b[\p{L}\p{M}]{3,}\b/u', $fullText, $matches);
            $words = $matches[0] ?? [];

            // Filtrar palabras comunes (stop words en español)
            $stopWords = ['el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'ser', 'se', 'no', 'haber', 'por', 'con', 'su', 'para', 'como', 'estar', 'tener', 'le', 'lo', 'todo', 'pero', 'más', 'hacer', 'o', 'poder', 'decir', 'este', 'ir', 'otro', 'ese', 'la', 'si', 'me', 'ya', 'ver', 'porque', 'dar', 'cuando', 'él', 'muy', 'sin', 'vez', 'mucho', 'saber', 'qué', 'sobre', 'mi', 'alguno', 'mismo', 'yo', 'también', 'hasta', 'año', 'dos', 'querer', 'entre', 'así', 'primero', 'desde', 'grande', 'eso', 'ni', 'nos', 'llegar', 'pasar', 'tiempo', 'ella', 'sí', 'día', 'uno', 'bien', 'poco', 'deber', 'entonces', 'poner', 'cosa', 'tanto', 'hombre', 'parecer', 'nuestro', 'tan', 'donde', 'ahora', 'parte', 'después', 'vida', 'quedar', 'siempre', 'creer', 'hablar', 'llevar', 'dejar', 'nada', 'cada', 'seguir', 'menos', 'nuevo', 'encontrar', 'algo', 'solo', 'mientras', 'poder', 'año', 'mil', 'hacer', 'aunque', 'menos', 'casa', 'trabajar', 'mujer', 'sin', 'seis', 'nunca', 'menos', 'mundo', 'hacer', 'año', 'mismo', 'año', 'año', 'año'];
            $filteredWords = array_filter($words, function($word) use ($stopWords) {
                // Limpiar y validar UTF-8
                $word = mb_convert_encoding($word, 'UTF-8', 'UTF-8');
                $word = trim($word);
                // Filtrar solo palabras válidas
                return mb_strlen($word, 'UTF-8') > 3
                    && !in_array($word, $stopWords)
                    && !is_numeric($word)
                    && mb_check_encoding($word, 'UTF-8');
            });

            $wordFreq = array_count_values($filteredWords);
            arsort($wordFreq);
            $topKeywords = array_slice($wordFreq, 0, 10, true);

            // Calcular densidad (frecuencia / total palabras * 100)
            $keywordDensity = [];
            foreach ($topKeywords as $keyword => $freq) {
                // Asegurar que la keyword es UTF-8 válida
                $keyword = mb_convert_encoding($keyword, 'UTF-8', 'UTF-8');
                if (!mb_check_encoding($keyword, 'UTF-8')) {
                    continue; // Saltar keywords inválidas
                }

                $density = ($freq / $wordCount) * 100;
                $keywordDensity[] = [
                    'keyword' => $keyword,
                    'frequency' => (int)$freq,
                    'density' => round($density, 2),
                ];
            }
            $result['keyword_density'] = $keywordDensity;

            // Sugerencias de contenido
            $suggestions = [];
            if ($wordCount < 300) {
                $suggestions[] = [
                    'type' => 'warning',
                    'message' => "El contenido tiene solo {$wordCount} palabras. Se recomienda al menos 300 palabras para mejor SEO.",
                ];
            } elseif ($wordCount < 500) {
                $suggestions[] = [
                    'type' => 'info',
                    'message' => "El contenido tiene {$wordCount} palabras. Se recomienda 500+ palabras para contenido de calidad.",
                ];
            }

            // Verificar densidad de keywords principales
            if (!empty($keywordDensity)) {
                $mainKeyword = $keywordDensity[0];
                // Asegurar que el mensaje es UTF-8 válido
                $keywordText = mb_convert_encoding($mainKeyword['keyword'], 'UTF-8', 'UTF-8');
                if ($mainKeyword['density'] < 0.5) {
                    $suggestions[] = [
                        'type' => 'warning',
                        'message' => "La densidad de la keyword principal '{$keywordText}' es baja ({$mainKeyword['density']}%). Se recomienda 1-2%.",
                    ];
                } elseif ($mainKeyword['density'] > 3) {
                    $suggestions[] = [
                        'type' => 'warning',
                        'message' => "La densidad de la keyword '{$keywordText}' es muy alta ({$mainKeyword['density']}%). Puede ser considerado keyword stuffing.",
                    ];
                }
            }

            // Asegurar que todas las sugerencias son UTF-8 válidas
            foreach ($suggestions as &$suggestion) {
                $suggestion['message'] = mb_convert_encoding($suggestion['message'], 'UTF-8', 'UTF-8');
            }
            unset($suggestion);

            $result['content_suggestions'] = $suggestions;
        } catch (Exception $e) {
            Log::warning("Error al analizar contenido: " . $e->getMessage());
            $result['word_count'] = 0;
            $result['keyword_density'] = [];
            $result['content_suggestions'] = [];
        }

        // Links - Guardar lista completa
        $result['internal_links'] = [];
        $result['external_links'] = [];
        $result['broken_links'] = [];

        try {
            $links = $crawler->filter('a[href]');
            $links->each(function (Crawler $link) use (&$result, $baseUrl, $url, $checkBrokenLinks) {
                $href = $link->attr('href');
                $text = trim($link->text());
                if (empty($href)) {
                    return;
                }

                // Normalizar URL
                $fullUrl = $this->normalizeUrl($href, $baseUrl);
                $parsedHref = parse_url($fullUrl);
                $hrefHost = $parsedHref['host'] ?? null;

                $linkData = [
                    'url' => $fullUrl,
                    'text' => $text ?: '(sin texto)',
                    'href' => $href, // URL original
                ];

                // Si es URL relativa, es interna
                if (!isset($parsedHref['host']) || $hrefHost === $baseUrl) {
                    $result['internal_links'][] = $linkData;
                    $result['internal_links_count']++;

                    // Solo verificar links rotos si se solicita (puede ser lento con muchos links)
                    if ($checkBrokenLinks) {
                        if ($this->checkBrokenLink($fullUrl)) {
                            $linkData['status_code'] = $this->getLinkStatus($fullUrl);
                            $result['broken_links'][] = $linkData;
                            $result['broken_links_count']++;
                        }
                    }
                } else {
                    $result['external_links'][] = $linkData;
                    $result['external_links_count']++;
                }
            });
        } catch (Exception $e) {
            Log::warning("Error al procesar links: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Normalizar URL
     */
    private function normalizeUrl($url, $baseDomain)
    {
        // Limpiar espacios
        $url = trim($url);
        $baseDomain = trim($baseDomain);

        // Limpiar barra final del dominio base si existe
        $baseDomain = rtrim($baseDomain, '/');

        // Si la URL ya tiene protocolo, usarla tal cual
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // Si es una URL relativa (empieza con /), agregar dominio base
        if (strpos($url, '/') === 0) {
            return 'https://' . $baseDomain . $url;
        }

        // Si no tiene protocolo, agregarlo con el dominio base
        // Si la URL contiene el dominio, solo agregar protocolo
        if (strpos($url, $baseDomain) !== false) {
            return 'https://' . ltrim($url, '/');
        }

        // Por defecto, agregar protocolo y dominio base
        return 'https://' . $baseDomain . '/' . ltrim($url, '/');
    }

    /**
     * Verificar si un link está roto
     */
    private function checkBrokenLink($url)
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->head($url);
            return $response->status() >= 400;
        } catch (Exception $e) {
            return true; // Si hay error, consideramos el link como roto
        }
    }

    /**
     * Obtener status code de un link
     */
    private function getLinkStatus($url)
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->head($url);
            return $response->status();
        } catch (Exception $e) {
            return 0; // Error desconocido
        }
    }

    /**
     * Generar alertas de contenido
     */
    private function generateContentAlerts(SeoAudit $audit, AuditResult $result)
    {
        $alertService = new \App\Services\AlertService();

        // Alerta si el contenido es muy corto
        if ($result->word_count && $result->word_count < 300) {
            $alertService->createContentAlert(
                $audit->site,
                $audit->url,
                'Contenido corto',
                "La página tiene solo {$result->word_count} palabras. Se recomienda al menos 300 palabras para mejor SEO."
            );
        }

        // Alerta si hay sugerencias de contenido críticas
        if (!empty($result->content_suggestions)) {
            foreach ($result->content_suggestions as $suggestion) {
                if ($suggestion['type'] == 'warning') {
                    $alertService->createContentAlert(
                        $audit->site,
                        $audit->url,
                        'Problema de contenido',
                        $suggestion['message']
                    );
                }
            }
        }
    }

    /**
     * Generar tareas automáticamente desde errores de auditoría
     */
    private function generateTasksFromAudit(SeoAudit $audit, AuditResult $result)
    {
        $errors = $result->errors ?? [];
        $warnings = $result->warnings ?? [];
        $tasksCreated = 0;

        // Mapeo de errores a tareas
        $errorTaskMap = [
            'No se encontró el tag <title>' => [
                'title' => 'Agregar título (title tag)',
                'description' => 'La página no tiene título. Es crítico para SEO.',
                'priority' => 'critical',
            ],
            'No se encontró ningún H1' => [
                'title' => 'Agregar H1 a la página',
                'description' => 'La página no tiene ningún encabezado H1. Es importante para SEO.',
                'priority' => 'high',
            ],
        ];

        // Crear tareas desde errores
        foreach ($errors as $error) {
            if (isset($errorTaskMap[$error])) {
                $taskData = $errorTaskMap[$error];

                // Verificar si ya existe una tarea similar para esta URL
                $existingTask = SeoTask::where('site_id', $audit->site_id)
                    ->where('url', $audit->url)
                    ->where('title', 'like', '%' . $taskData['title'] . '%')
                    ->where('status', '!=', 'completed')
                    ->first();

                if (!$existingTask) {
                    SeoTask::create([
                        'site_id' => $audit->site_id,
                        'seo_audit_id' => $audit->id,
                        'created_by' => null, // Sistema
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] . "\n\nError detectado: {$error}",
                        'url' => $audit->url,
                        'priority' => $taskData['priority'],
                        'status' => 'pending',
                    ]);
                    $tasksCreated++;
                }
            } else {
                // Tarea genérica para otros errores
                SeoTask::create([
                    'site_id' => $audit->site_id,
                    'seo_audit_id' => $audit->id,
                    'created_by' => null,
                    'title' => 'Corregir error SEO: ' . Str::limit($error, 50),
                    'description' => "Error detectado en la auditoría:\n{$error}",
                    'url' => $audit->url,
                    'priority' => 'high',
                    'status' => 'pending',
                ]);
                $tasksCreated++;
            }
        }

        // Crear tareas desde advertencias críticas
        foreach ($warnings as $warning) {
            if (strpos($warning, 'imágenes sin atributo ALT') !== false) {
                // Extraer número de imágenes
                preg_match('/(\d+)\s+imágenes sin atributo ALT/', $warning, $matches);
                $count = $matches[1] ?? 0;

                if ($count > 0) {
                    $existingTask = SeoTask::where('site_id', $audit->site_id)
                        ->where('url', $audit->url)
                        ->where('title', 'like', '%imágenes sin ALT%')
                        ->where('status', '!=', 'completed')
                        ->first();

                    if (!$existingTask) {
                        SeoTask::create([
                            'site_id' => $audit->site_id,
                            'seo_audit_id' => $audit->id,
                            'created_by' => null,
                            'title' => "Agregar atributo ALT a {$count} imágenes",
                            'description' => "Se detectaron {$count} imágenes sin atributo ALT. Es importante para accesibilidad y SEO.",
                            'url' => $audit->url,
                            'priority' => $count > 5 ? 'high' : 'medium',
                            'status' => 'pending',
                        ]);
                        $tasksCreated++;
                    }
                }
            }
        }

        if ($tasksCreated > 0) {
            Log::info("Se generaron {$tasksCreated} tareas automáticamente desde la auditoría {$audit->id}");
        }
    }
}

