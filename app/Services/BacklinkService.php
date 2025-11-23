<?php

namespace App\Services;

use App\Models\Backlink;
use App\Models\Site;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BacklinkService
{
    /**
     * Sincronizar backlinks desde Google Search Console
     * Nota: Esto requiere autenticación OAuth con GSC
     * Por ahora, implementamos la estructura básica
     */
    public function syncFromGSC(Site $site)
    {
        try {
            // TODO: Implementar autenticación OAuth con Google Search Console
            // Por ahora, retornamos un mensaje informativo

            Log::info("Sincronización de backlinks desde GSC para sitio {$site->id}");

            // Ejemplo de cómo sería la implementación:
            // $gscData = $this->fetchGSCBacklinks($site);
            // foreach ($gscData as $backlink) {
            //     $this->createOrUpdateBacklink($site, $backlink, 'gsc');
            // }

            return [
                'success' => false,
                'message' => 'La integración con Google Search Console requiere configuración OAuth. Por favor, agrega backlinks manualmente o configura las credenciales de GSC.',
            ];
        } catch (\Exception $e) {
            Log::error("Error al sincronizar backlinks desde GSC: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al sincronizar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Crear o actualizar un backlink
     */
    public function createOrUpdateBacklink(Site $site, array $data, $sourceType = 'manual')
    {
        try {
            // Extraer dominio de la URL fuente
            $sourceDomain = parse_url($data['source_url'], PHP_URL_HOST);

            // Detectar si es tóxico
            $isToxic = $this->detectToxicBacklink($data);
            $toxicReason = $isToxic ? $this->getToxicReason($data) : null;

            $backlink = Backlink::updateOrCreate(
                [
                    'site_id' => $site->id,
                    'source_url' => $data['source_url'],
                    'target_url' => $data['target_url'],
                ],
                [
                    'source_domain' => $sourceDomain,
                    'anchor_text' => $data['anchor_text'] ?? null,
                    'link_type' => $data['link_type'] ?? 'dofollow',
                    'first_seen' => $data['first_seen'] ?? Carbon::today(),
                    'last_seen' => Carbon::today(),
                    'domain_authority' => $data['domain_authority'] ?? null,
                    'page_authority' => $data['page_authority'] ?? null,
                    'source_type' => $sourceType,
                    'is_toxic' => $isToxic,
                    'toxic_reason' => $toxicReason,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            return $backlink;
        } catch (\Exception $e) {
            Log::error("Error al crear/actualizar backlink: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Detectar si un backlink es tóxico (heurística básica)
     */
    private function detectToxicBacklink(array $data)
    {
        $sourceDomain = parse_url($data['source_url'], PHP_URL_HOST);
        $sourceUrl = $data['source_url'] ?? '';
        $anchorText = strtolower($data['anchor_text'] ?? '');

        // Lista de palabras clave sospechosas
        $toxicKeywords = [
            'casino', 'poker', 'gambling', 'viagra', 'cialis', 'loan', 'debt',
            'payday', 'forex', 'binary', 'crypto', 'bitcoin', 'escort', 'adult',
            'porn', 'xxx', 'sex', 'dating', 'hookup', 'penis', 'enlargement',
        ];

        // Verificar dominio sospechoso
        foreach ($toxicKeywords as $keyword) {
            if (stripos($sourceDomain, $keyword) !== false) {
                return true;
            }
        }

        // Verificar anchor text sospechoso
        foreach ($toxicKeywords as $keyword) {
            if (stripos($anchorText, $keyword) !== false) {
                return true;
            }
        }

        // Verificar dominios de spam conocidos (lista básica)
        $spamDomains = [
            '.tk', '.ml', '.ga', '.cf', // Dominios gratuitos sospechosos
        ];

        foreach ($spamDomains as $spamDomain) {
            if (stripos($sourceDomain, $spamDomain) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener razón por la que se marca como tóxico
     */
    private function getToxicReason(array $data)
    {
        $sourceDomain = parse_url($data['source_url'], PHP_URL_HOST);
        $anchorText = strtolower($data['anchor_text'] ?? '');

        $reasons = [];

        $toxicKeywords = [
            'casino', 'poker', 'gambling', 'viagra', 'cialis', 'loan', 'debt',
            'payday', 'forex', 'binary', 'crypto', 'bitcoin', 'escort', 'adult',
        ];

        foreach ($toxicKeywords as $keyword) {
            if (stripos($sourceDomain, $keyword) !== false) {
                $reasons[] = "Dominio contiene palabra clave sospechosa: {$keyword}";
            }
            if (stripos($anchorText, $keyword) !== false) {
                $reasons[] = "Anchor text contiene palabra clave sospechosa: {$keyword}";
            }
        }

        $spamDomains = ['.tk', '.ml', '.ga', '.cf'];
        foreach ($spamDomains as $spamDomain) {
            if (stripos($sourceDomain, $spamDomain) !== false) {
                $reasons[] = "Dominio de tipo sospechoso: {$spamDomain}";
            }
        }

        return implode('; ', $reasons) ?: 'Backlink marcado como tóxico por heurística';
    }

    /**
     * Importar backlinks desde CSV
     */
    public function importFromCSV(Site $site, $filePath)
    {
        try {
            $handle = fopen($filePath, 'r');
            $header = fgetcsv($handle); // Leer encabezados

            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                try {
                    $this->createOrUpdateBacklink($site, $data, 'manual');
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error en fila: " . $e->getMessage();
                }
            }

            fclose($handle);

            return [
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            Log::error("Error al importar backlinks desde CSV: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al importar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener estadísticas de backlinks para un sitio
     */
    public function getStatistics(Site $site)
    {
        $backlinks = Backlink::where('site_id', $site->id)->get();

        return [
            'total' => $backlinks->count(),
            'dofollow' => $backlinks->where('link_type', 'dofollow')->count(),
            'nofollow' => $backlinks->where('link_type', 'nofollow')->count(),
            'toxic' => $backlinks->where('is_toxic', true)->count(),
            'domains' => $backlinks->pluck('source_domain')->unique()->count(),
            'from_gsc' => $backlinks->where('source_type', 'gsc')->count(),
            'manual' => $backlinks->where('source_type', 'manual')->count(),
        ];
    }
}

