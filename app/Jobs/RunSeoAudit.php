<?php

namespace App\Jobs;

use App\Models\Site;
use App\Services\SeoAuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunSeoAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $url;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param string $url
     */
    public function __construct(Site $site, $url)
    {
        $this->site = $site;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @param SeoAuditService $auditService
     * @return void
     */
    public function handle(SeoAuditService $auditService)
    {
        try {
            // Aumentar tiempo de ejecución para jobs (6 minutos)
            set_time_limit(360);

            Log::info("Iniciando auditoría SEO para {$this->url} del sitio {$this->site->id}");

            // Ejecutar auditoría sin verificar links rotos (más rápido)
            $audit = $auditService->auditUrl($this->site, $this->url, false);

            Log::info("Auditoría completada para {$this->url}");

            // Si hay más de 30 links internos, verificar links rotos en segundo plano
            if ($audit->result && count($audit->result->internal_links ?? []) > 30) {
                Log::info("Encolando verificación de links rotos para auditoría {$audit->id} (más de 30 links)");
                \App\Jobs\CheckBrokenLinks::dispatch($audit->id);
            } elseif ($audit->result && count($audit->result->internal_links ?? []) > 0) {
                // Si hay menos de 30 links, verificarlos inmediatamente
                Log::info("Verificando links rotos inmediatamente para auditoría {$audit->id} (menos de 30 links)");
                \App\Jobs\CheckBrokenLinks::dispatch($audit->id);
            }
        } catch (\Exception $e) {
            Log::error("Error en auditoría SEO para {$this->url}: " . $e->getMessage());
            throw $e;
        }
    }
}
