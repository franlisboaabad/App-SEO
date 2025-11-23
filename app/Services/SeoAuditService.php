<?php

namespace App\Services;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Models\AuditResult;
use App\Models\SeoTask;
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

                // Guardar resultados
                $auditResult = AuditResult::create([
                    'seo_audit_id' => $audit->id,
                    ...$result,
                ]);

                // Actualizar estado de la auditoría
                $audit->update(['status' => 'completed']);

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

