<?php

namespace App\Services;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Models\AuditResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class SeoAuditService
{
    /**
     * Ejecutar auditoría SEO para una URL
     */
    public function auditUrl(Site $site, $url)
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
                    $result = $this->analyzePage($crawler, $url, $ttfb, $statusCode);
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
     */
    private function analyzePage(Crawler $crawler, $url, $ttfb, $statusCode)
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

        // Links
        try {
            $links = $crawler->filter('a[href]');
            $links->each(function (Crawler $link) use (&$result, $baseUrl, $url) {
                $href = $link->attr('href');
                if (empty($href)) {
                    return;
                }

                $parsedHref = parse_url($href);
                $hrefHost = $parsedHref['host'] ?? null;

                // Si es URL relativa, es interna
                if (!isset($parsedHref['host'])) {
                    $result['internal_links_count']++;
                } elseif ($hrefHost === $baseUrl) {
                    $result['internal_links_count']++;
                } else {
                    $result['external_links_count']++;
                }

                // Verificar si el link está roto (solo para links internos)
                if (!isset($parsedHref['host']) || $hrefHost === $baseUrl) {
                    // Aquí podríamos hacer una verificación adicional, pero por ahora solo contamos
                    // En una versión mejorada, podríamos hacer una petición HEAD para verificar
                }
            });
        } catch (Exception $e) {
            // Ignorar
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
            $response = Http::timeout(10)->head($url);
            return $response->status() >= 400;
        } catch (Exception $e) {
            return true; // Si hay error, consideramos el link como roto
        }
    }
}

