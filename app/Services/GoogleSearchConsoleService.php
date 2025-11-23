<?php

namespace App\Services;

use App\Models\Site;
use App\Models\SeoMetric;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSearchConsoleService
{
    /**
     * Obtener token de acceso usando credenciales JSON
     * Las credenciales deben contener: access_token, refresh_token, client_id, client_secret
     */
    private function getAccessToken($credentials)
    {
        try {
            // Decodificar credenciales JSON
            if (is_string($credentials)) {
                $credentials = json_decode($credentials, true);
            }

            // Si hay access_token y no está expirado, usarlo
            if (isset($credentials['access_token']) && isset($credentials['expires_at'])) {
                $expiresAt = Carbon::parse($credentials['expires_at']);
                if ($expiresAt->isFuture()) {
                    return $credentials['access_token'];
                }
            }

            // Si hay refresh_token, refrescar
            if (isset($credentials['refresh_token'])) {
                return $this->refreshAccessToken($credentials['refresh_token'], $credentials);
            }

            // Si hay access_token directo (sin expiración configurada)
            if (isset($credentials['access_token'])) {
                return $credentials['access_token'];
            }

            throw new \Exception('No se encontró access_token o refresh_token en las credenciales');
        } catch (\Exception $e) {
            Log::error('Error obteniendo token GSC: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refrescar token de acceso usando refresh_token
     */
    private function refreshAccessToken($refreshToken, $credentials)
    {
        if (!isset($credentials['client_id']) || !isset($credentials['client_secret'])) {
            throw new \Exception('client_id y client_secret son requeridos para refrescar el token');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'];
        }

        throw new \Exception('Error al refrescar token: ' . $response->body());
    }

    /**
     * Obtener métricas de Google Search Console
     */
    public function fetchMetrics(Site $site, $startDate = null, $endDate = null, $dimensions = ['page', 'query'])
    {
        try {
            if (!$site->gsc_property || !$site->gsc_credentials) {
                throw new \Exception('Sitio no tiene configuradas las credenciales de GSC');
            }

            // Fechas por defecto: últimos 30 días
            $endDate = $endDate ?: Carbon::yesterday()->format('Y-m-d');
            $startDate = $startDate ?: Carbon::parse($endDate)->subDays(30)->format('Y-m-d');

            // Obtener token
            $accessToken = $this->getAccessToken($site->gsc_credentials);

            // Construir URL de la API
            $url = 'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site->gsc_property) . '/searchAnalytics/query';

            // Preparar request
            $requestData = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dimensions' => $dimensions,
                'rowLimit' => 25000, // Máximo permitido
            ];

            $response = Http::withToken($accessToken)
                ->post($url, $requestData);

            if (!$response->successful()) {
                throw new \Exception('Error en API GSC: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas GSC para sitio ' . $site->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sincronizar métricas y guardarlas en la base de datos
     * Sincroniza día por día para tener datos históricos precisos
     */
    public function syncMetrics(Site $site, $startDate = null, $endDate = null)
    {
        try {
            $endDate = $endDate ?: Carbon::yesterday()->format('Y-m-d');
            $startDate = $startDate ?: Carbon::parse($endDate)->subDays(30)->format('Y-m-d');

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $totalSaved = 0;

            // Sincronizar día por día para obtener datos precisos
            while ($start->lte($end)) {
                $currentDate = $start->format('Y-m-d');

                // Obtener métricas por página, keyword y fecha
                $data = $this->fetchMetrics($site, $currentDate, $currentDate, ['page', 'query', 'date']);

                if (isset($data['rows']) && !empty($data['rows'])) {
                    foreach ($data['rows'] as $row) {
                        // Extraer datos según las dimensiones
                        $keys = $row['keys'] ?? [];
                        $url = $keys[0] ?? null; // page
                        $keyword = $keys[1] ?? null; // query
                        $date = isset($keys[2]) ? $keys[2] : $currentDate; // date
                        $device = 'desktop'; // Por defecto

                        // Guardar o actualizar métrica
                        SeoMetric::updateOrCreate(
                            [
                                'site_id' => $site->id,
                                'url' => $url,
                                'keyword' => $keyword,
                                'device' => $device,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                            ],
                            [
                                'clicks' => $row['clicks'] ?? 0,
                                'impressions' => $row['impressions'] ?? 0,
                                'ctr' => $row['ctr'] ?? 0,
                                'position' => $row['position'] ?? 0,
                            ]
                        );
                        $totalSaved++;
                    }
                }

                $start->addDay();
            }

            Log::info("Sincronizadas {$totalSaved} métricas para el sitio {$site->id} desde {$startDate} hasta {$endDate}");
            return $totalSaved;
        } catch (\Exception $e) {
            Log::error('Error sincronizando métricas para sitio ' . $site->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener métricas agregadas por fecha
     */
    public function getAggregatedMetrics(Site $site, $startDate = null, $endDate = null)
    {
        try {
            $endDate = $endDate ?: Carbon::yesterday()->format('Y-m-d');
            $startDate = $startDate ?: Carbon::parse($endDate)->subDays(30)->format('Y-m-d');

            $data = $this->fetchMetrics($site, $startDate, $endDate, ['date']);

            if (!isset($data['rows'])) {
                return [];
            }

            return $data['rows'];
        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas agregadas: ' . $e->getMessage());
            return [];
        }
    }
}

