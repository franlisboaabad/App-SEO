<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backlink;
use App\Models\Site;
use App\Services\BacklinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BacklinkController extends Controller
{
    protected $backlinkService;

    public function __construct(BacklinkService $backlinkService)
    {
        $this->backlinkService = $backlinkService;
    }

    /**
     * Listar backlinks
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');
        $query = Backlink::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        // Filtros
        if ($request->has('toxic') && $request->toxic == '1') {
            $query->where('is_toxic', true);
        }

        if ($request->has('link_type') && $request->link_type) {
            $query->where('link_type', $request->link_type);
        }

        if ($request->has('source_type') && $request->source_type) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('source_domain', 'like', "%{$search}%")
                  ->orWhere('source_url', 'like', "%{$search}%")
                  ->orWhere('target_url', 'like', "%{$search}%")
                  ->orWhere('anchor_text', 'like', "%{$search}%");
            });
        }

        $backlinks = $query->orderBy('created_at', 'desc')->paginate(20);
        $sites = Site::active()->get();

        // Estadísticas
        $stats = $siteId ? $this->backlinkService->getStatistics(Site::find($siteId)) : null;

        return view('admin.backlinks.index', compact('backlinks', 'sites', 'siteId', 'stats'));
    }

    /**
     * Mostrar formulario para crear backlink
     */
    public function create(Request $request)
    {
        $siteId = $request->get('site_id');
        $sites = Site::active()->get();

        return view('admin.backlinks.create', compact('sites', 'siteId'));
    }

    /**
     * Guardar nuevo backlink
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'source_url' => 'required|url',
            'target_url' => 'required|url',
            'anchor_text' => 'nullable|string|max:500',
            'link_type' => 'required|in:dofollow,nofollow,sponsored,ugc',
            'first_seen' => 'nullable|date',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'page_authority' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $site = Site::findOrFail($validated['site_id']);
            $backlink = $this->backlinkService->createOrUpdateBacklink($site, $validated, 'manual');

            return redirect()->route('backlinks.index', ['site_id' => $site->id])
                ->with('success', 'Backlink agregado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al crear backlink: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear backlink: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar backlink específico
     */
    public function show(Backlink $backlink)
    {
        $backlink->load('site');
        return view('admin.backlinks.show', compact('backlink'));
    }

    /**
     * Mostrar formulario para editar backlink
     */
    public function edit(Backlink $backlink)
    {
        $sites = Site::active()->get();
        return view('admin.backlinks.edit', compact('backlink', 'sites'));
    }

    /**
     * Actualizar backlink
     */
    public function update(Request $request, Backlink $backlink)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'source_url' => 'required|url',
            'target_url' => 'required|url',
            'anchor_text' => 'nullable|string|max:500',
            'link_type' => 'required|in:dofollow,nofollow,sponsored,ugc',
            'first_seen' => 'nullable|date',
            'last_seen' => 'nullable|date',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'page_authority' => 'nullable|integer|min:0|max:100',
            'is_toxic' => 'boolean',
            'toxic_reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $sourceDomain = parse_url($validated['source_url'], PHP_URL_HOST);

            $backlink->update([
                'site_id' => $validated['site_id'],
                'source_domain' => $sourceDomain,
                'source_url' => $validated['source_url'],
                'target_url' => $validated['target_url'],
                'anchor_text' => $validated['anchor_text'] ?? null,
                'link_type' => $validated['link_type'],
                'first_seen' => $validated['first_seen'] ?? null,
                'last_seen' => $validated['last_seen'] ?? null,
                'domain_authority' => $validated['domain_authority'] ?? null,
                'page_authority' => $validated['page_authority'] ?? null,
                'is_toxic' => $validated['is_toxic'] ?? false,
                'toxic_reason' => $validated['toxic_reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('backlinks.index', ['site_id' => $backlink->site_id])
                ->with('success', 'Backlink actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar backlink: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar backlink: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar backlink
     */
    public function destroy(Backlink $backlink)
    {
        try {
            $siteId = $backlink->site_id;
            $backlink->delete();

            return redirect()->route('backlinks.index', ['site_id' => $siteId])
                ->with('success', 'Backlink eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar backlink: " . $e->getMessage());
            return back()->with('error', 'Error al eliminar backlink: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar desde Google Search Console
     */
    public function syncFromGSC(Site $site)
    {
        try {
            $result = $this->backlinkService->syncFromGSC($site);

            if ($result['success']) {
                return redirect()->route('backlinks.index', ['site_id' => $site->id])
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('backlinks.index', ['site_id' => $site->id])
                    ->with('warning', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error("Error al sincronizar desde GSC: " . $e->getMessage());
            return back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }

    /**
     * Importar desde CSV
     */
    public function importCSV(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $site = Site::findOrFail($validated['site_id']);
            $filePath = $request->file('csv_file')->getRealPath();

            $result = $this->backlinkService->importFromCSV($site, $filePath);

            if ($result['success']) {
                return redirect()->route('backlinks.index', ['site_id' => $site->id])
                    ->with('success', "Se importaron {$result['imported']} backlinks correctamente.");
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error("Error al importar CSV: " . $e->getMessage());
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard de backlinks para un sitio
     */
    public function dashboard(Site $site)
    {
        $stats = $this->backlinkService->getStatistics($site);
        $backlinks = Backlink::where('site_id', $site->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topDomains = Backlink::where('site_id', $site->id)
            ->selectRaw('source_domain, COUNT(*) as count')
            ->groupBy('source_domain')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.backlinks.dashboard', compact('site', 'stats', 'backlinks', 'topDomains'));
    }
}
