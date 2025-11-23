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
            Log::info("Iniciando auditoría SEO para {$this->url} del sitio {$this->site->id}");
            $auditService->auditUrl($this->site, $this->url);
            Log::info("Auditoría completada para {$this->url}");
        } catch (\Exception $e) {
            Log::error("Error en auditoría SEO para {$this->url}: " . $e->getMessage());
            throw $e;
        }
    }
}
