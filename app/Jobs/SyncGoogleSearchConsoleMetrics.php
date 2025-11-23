<?php

namespace App\Jobs;

use App\Models\Site;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncGoogleSearchConsoleMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $startDate;
    public $endDate;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function __construct(Site $site, $startDate = null, $endDate = null)
    {
        $this->site = $site;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @param GoogleSearchConsoleService $gscService
     * @return void
     */
    public function handle(GoogleSearchConsoleService $gscService)
    {
        try {
            if (!$this->site->estado) {
                Log::info("Sitio {$this->site->id} está inactivo, saltando sincronización");
                return;
            }

            if (!$this->site->gsc_property || !$this->site->gsc_credentials) {
                Log::warning("Sitio {$this->site->id} no tiene configurado GSC, saltando sincronización");
                return;
            }

            Log::info("Iniciando sincronización de métricas GSC para sitio {$this->site->id}");

            $saved = $gscService->syncMetrics($this->site, $this->startDate, $this->endDate);

            Log::info("Sincronización completada para sitio {$this->site->id}: {$saved} métricas guardadas");
        } catch (\Exception $e) {
            Log::error("Error en job de sincronización GSC para sitio {$this->site->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
