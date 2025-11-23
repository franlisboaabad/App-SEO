<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Jobs\SyncGoogleSearchConsoleMetrics;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncAllSitesMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:sync-metrics {--site= : ID del sitio específico} {--days=1 : Número de días a sincronizar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza métricas de Google Search Console para todos los sitios activos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siteId = $this->option('site');
        $days = (int) $this->option('days');

        $endDate = Carbon::yesterday()->format('Y-m-d');
        $startDate = Carbon::parse($endDate)->subDays($days - 1)->format('Y-m-d');

        $this->info("Sincronizando métricas desde {$startDate} hasta {$endDate}");

        // Obtener sitios a sincronizar
        $sites = $siteId
            ? Site::where('id', $siteId)->where('estado', true)->get()
            : Site::where('estado', true)->get();

        if ($sites->isEmpty()) {
            $this->warn('No hay sitios activos para sincronizar');
            return Command::FAILURE;
        }

        $this->info("Encontrados {$sites->count()} sitio(s) para sincronizar");

        foreach ($sites as $site) {
            if (!$site->gsc_property || !$site->gsc_credentials) {
                $this->warn("Sitio {$site->id} ({$site->nombre}) no tiene configurado GSC, saltando...");
                continue;
            }

            $this->info("Encolando sincronización para sitio: {$site->nombre} (ID: {$site->id})");

            SyncGoogleSearchConsoleMetrics::dispatch($site, $startDate, $endDate);
        }

        $this->info('Todas las sincronizaciones han sido encoladas. Ejecutándose en segundo plano...');
        return Command::SUCCESS;
    }
}
