<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\KeywordResearch;
use App\Services\KeywordResearchService;
use App\Exports\KeywordResearchExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KeywordResearchController extends Controller
{
    protected $researchService;

    public function __construct(KeywordResearchService $researchService)
    {
        $this->middleware('auth');
        $this->researchService = $researchService;
    }

    /**
     * Mostrar página de investigación de keywords
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');
        $source = $request->get('source');
        $intent = $request->get('intent');
        $untrackedOnly = $request->get('untracked_only', false);

        $query = KeywordResearch::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        if ($source) {
            $query->where('source', $source);
        }

        if ($intent) {
            $query->where('intent', $intent);
        }

        if ($untrackedOnly) {
            $query->untracked();
        }

        // Para DataTables, obtener todos los resultados sin paginación
        $keywords = $query->latest()->get();
        $sites = Site::active()->get();

        // Estadísticas
        $totalKeywords = KeywordResearch::when($siteId, function($q) use ($siteId) {
            return $q->where('site_id', $siteId);
        })->count();

        $untrackedCount = KeywordResearch::when($siteId, function($q) use ($siteId) {
            return $q->where('site_id', $siteId);
        })->untracked()->count();

        return view('admin.keyword-research.index', compact(
            'keywords',
            'sites',
            'siteId',
            'source',
            'intent',
            'untrackedOnly',
            'totalKeywords',
            'untrackedCount'
        ));
    }

    /**
     * Buscar keywords desde GSC
     */
    public function searchFromGSC(Request $request, Site $site)
    {
        $limit = $request->get('limit', 50);

        $results = $this->researchService->searchFromGSC($site, $limit);

        return back()->with('success', "Se encontraron " . count($results) . " keywords desde Google Search Console.");
    }

    /**
     * Buscar keywords relacionadas usando autocomplete
     */
    public function searchRelated(Request $request)
    {
        $request->validate([
            'site' => 'required|exists:sites,id',
            'seed_keyword' => 'required|string|max:255',
            'depth' => 'nullable|integer|min:1|max:2',
        ]);

        $site = Site::findOrFail($request->get('site'));
        $seedKeyword = $request->get('seed_keyword');
        $depth = $request->get('depth', 1);

        $results = $this->researchService->searchRelatedKeywords($site, $seedKeyword, $depth);

        return redirect()->route('keyword-research.index', ['site_id' => $site->id])
            ->with('success', "Se encontraron " . count($results) . " keywords relacionadas con '{$seedKeyword}'.");
    }

    /**
     * Agregar keyword al tracking
     */
    public function addToTracking(KeywordResearch $keywordResearch, Request $request)
    {
        $targetUrl = $request->get('target_url');

        $added = $this->researchService->addToTracking($keywordResearch, $targetUrl);

        if ($added) {
            return back()->with('success', "Keyword '{$keywordResearch->keyword}' agregada al tracking.");
        } else {
            return back()->with('error', "La keyword '{$keywordResearch->keyword}' ya está siendo trackeada.");
        }
    }

    /**
     * Actualizar datos de investigación
     */
    public function update(KeywordResearch $keywordResearch, Request $request)
    {
        $validated = $request->validate([
            'search_volume' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|numeric|min:0|max:100',
            'cpc' => 'nullable|numeric|min:0',
            'intent' => 'nullable|in:informational,navigational,transactional,commercial',
            'notes' => 'nullable|string',
        ]);

        $keywordResearch->update($validated);

        return back()->with('success', 'Datos de investigación actualizados.');
    }

    /**
     * Eliminar keyword de investigación
     */
    public function destroy(KeywordResearch $keywordResearch)
    {
        $keyword = $keywordResearch->keyword;
        $keywordResearch->delete();

        return back()->with('success', "Keyword '{$keyword}' eliminada de la investigación.");
    }

    /**
     * Asignar clusters automáticamente
     */
    public function assignClusters(Request $request, Site $site)
    {
        $updated = $this->researchService->assignClusters($site);

        return redirect()->route('keyword-research.index', ['site_id' => $site->id])
            ->with('success', "Se asignaron clusters a {$updated} keywords.");
    }

    /**
     * Vista de clusters
     */
    public function clusters(Request $request, Site $site = null)
    {
        $siteId = $request->get('site_id') ?? ($site ? $site->id : null);

        $query = KeywordResearch::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $keywords = $query->whereNotNull('cluster')->get();

        // Agrupar por cluster
        $clusters = $keywords->groupBy('cluster')->map(function ($group) {
            return [
                'keywords' => $group,
                'count' => $group->count(),
                'avg_volume' => $group->avg('search_volume'),
                'intents' => $group->pluck('intent')->unique()->values(),
            ];
        })->sortByDesc('count');

        $sites = Site::active()->get();

        return view('admin.keyword-research.clusters', compact('clusters', 'sites', 'siteId'));
    }

    /**
     * Analizar intención de keywords
     */
    public function analyzeIntent(Request $request, Site $site)
    {
        $keywords = KeywordResearch::where('site_id', $site->id)
            ->whereNull('intent')
            ->get();

        $updated = 0;
        foreach ($keywords as $keyword) {
            $intent = $this->researchService->detectIntent($keyword->keyword);
            $keyword->update(['intent' => $intent]);
            $updated++;
        }

        return redirect()->route('keyword-research.index', ['site_id' => $site->id])
            ->with('success', "Se analizó la intención de {$updated} keywords.");
    }

    /**
     * Exportar investigación de keywords a Excel
     */
    public function export(Request $request)
    {
        $siteId = $request->get('site_id');
        $siteName = $siteId ? Site::find($siteId)->nombre ?? 'Todos' : 'Todos';
        $filename = 'investigacion_keywords_' . str_replace(' ', '_', $siteName) . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new KeywordResearchExport($siteId), $filename);
    }
}
