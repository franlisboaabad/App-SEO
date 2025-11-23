<?php

namespace App\Jobs;

use App\Models\SeoAudit;
use App\Models\AuditResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckBrokenLinks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $auditId;

    /**
     * Create a new job instance.
     *
     * @param int $auditId
     */
    public function __construct($auditId)
    {
        $this->auditId = $auditId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $audit = SeoAudit::with('result')->findOrFail($this->auditId);

            if (!$audit->result) {
                Log::warning("No hay resultado de auditoría para verificar links rotos: {$this->auditId}");
                return;
            }

            $result = $audit->result;
            $internalLinks = $result->internal_links ?? [];

            if (empty($internalLinks)) {
                Log::info("No hay links internos para verificar en auditoría: {$this->auditId}");
                return;
            }

            Log::info("Iniciando verificación de " . count($internalLinks) . " links internos para auditoría {$this->auditId}");

            $brokenLinks = [];
            $brokenLinksCount = 0;

            foreach ($internalLinks as $link) {
                $url = $link['url'] ?? null;
                if (!$url) {
                    continue;
                }

                try {
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->head($url);

                    if ($response->status() >= 400) {
                        $link['status_code'] = $response->status();
                        $brokenLinks[] = $link;
                        $brokenLinksCount++;
                    }
                } catch (\Exception $e) {
                    // Si hay error, consideramos el link como roto
                    $link['status_code'] = 0;
                    $brokenLinks[] = $link;
                    $brokenLinksCount++;
                }
            }

            // Actualizar resultado con links rotos
            $result->update([
                'broken_links' => $brokenLinks,
                'broken_links_count' => $brokenLinksCount,
            ]);

            Log::info("Verificación de links completada para auditoría {$this->auditId}. Links rotos encontrados: {$brokenLinksCount}");
        } catch (\Exception $e) {
            Log::error("Error al verificar links rotos para auditoría {$this->auditId}: " . $e->getMessage());
            throw $e;
        }
    }
}
