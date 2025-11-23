<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\AlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidateSitemapRobots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:validate-sitemap-robots {site_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar sitemap.xml y robots.txt de los sitios';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siteId = $this->argument('site_id');

        $sites = $siteId
            ? collect([Site::findOrFail($siteId)])
            : Site::active()->get();

        $alertService = new AlertService();
        $validated = 0;
        $errors = 0;

        foreach ($sites as $site) {
            $this->info("Validando sitio: {$site->nombre} ({$site->dominio_base})");

            // Validar sitemap.xml
            $sitemapUrl = "https://{$site->dominio_base}/sitemap.xml";
            $sitemapValid = $this->validateSitemap($sitemapUrl);

            if (!$sitemapValid) {
                $alertService->createErrorAlert(
                    $site,
                    $sitemapUrl,
                    'Sitemap no encontrado o inválido',
                    "El sitemap.xml no se encuentra o no es válido en {$sitemapUrl}"
                );
                $errors++;
            } else {
                $this->info("  ✓ Sitemap válido");
            }

            // Validar robots.txt
            $robotsUrl = "https://{$site->dominio_base}/robots.txt";
            $robotsValid = $this->validateRobots($robotsUrl);

            if (!$robotsValid) {
                $alertService->createErrorAlert(
                    $site,
                    $robotsUrl,
                    'Robots.txt no encontrado o inválido',
                    "El robots.txt no se encuentra o no es válido en {$robotsUrl}"
                );
                $errors++;
            } else {
                $this->info("  ✓ Robots.txt válido");
            }

            $validated++;
        }

        $this->info("\nValidación completada: {$validated} sitios, {$errors} errores encontrados");

        return 0;
    }

    /**
     * Validar sitemap.xml
     */
    private function validateSitemap($url)
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($url);

            if ($response->successful()) {
                $content = $response->body();
                // Verificar que sea XML válido
                $xml = @simplexml_load_string($content);
                return $xml !== false;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning("Error al validar sitemap {$url}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validar robots.txt
     */
    private function validateRobots($url)
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($url);

            if ($response->successful()) {
                $content = $response->body();
                // Verificar que tenga contenido básico
                return !empty(trim($content));
            }

            return false;
        } catch (\Exception $e) {
            Log::warning("Error al validar robots.txt {$url}: " . $e->getMessage());
            return false;
        }
    }
}
