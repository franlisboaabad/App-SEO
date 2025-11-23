<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Jobs\RunSeoAudit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeoAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.audits.index')->only('index');
        $this->middleware('can:admin.audits.show')->only('show');
        $this->middleware('can:admin.audits.run')->only('runAudit');
    }

    /**
     * Ejecutar auditoría para una URL
     */
    public function runAudit(Request $request, Site $site)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            // Encolar auditoría
            RunSeoAudit::dispatch($site, $request->url);

            return back()->with('success', 'Auditoría encolada correctamente. Se procesará en segundo plano.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al encolar auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de auditorías de un sitio
     */
    public function index(Site $site)
    {
        $audits = $site->seoAudits()
            ->with('result')
            ->latest()
            ->paginate(20);

        return view('admin.sites.audits', compact('site', 'audits'));
    }

    /**
     * Ver detalles de una auditoría
     */
    public function show(SeoAudit $audit)
    {
        $audit->load('result', 'site');

        return view('admin.sites.audit-detail', compact('audit'));
    }
}
