<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Keyword;
use App\Models\SerpAnalysis;
use App\Services\SerpAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SerpAnalysisController extends Controller
{
    protected $serpService;

    public function __construct(SerpAnalysisService $serpService)
    {
        $this->middleware('auth');
        $this->serpService = $serpService;
    }

    /**
     * Listar análisis de SERP
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');
        $keyword = $request->get('keyword');

        $query = SerpAnalysis::with('site', 'keyword')->latest('analysis_date');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        if ($keyword) {
            $query->where('keyword', 'like', "%{$keyword}%");
        }

        $analyses = $query->paginate(20);
        $sites = Site::active()->get();

        return view('admin.serp-analysis.index', compact('analyses', 'sites', 'siteId', 'keyword'));
    }

    /**
     * Mostrar formulario para analizar SERP
     */
    public function create(Request $request)
    {
        $siteId = $request->get('site_id');
        $keywordId = $request->get('keyword_id');

        $sites = Site::active()->get();
        $keywords = $siteId ? Keyword::where('site_id', $siteId)->where('is_active', true)->get() : collect();

        $keyword = $keywordId ? Keyword::find($keywordId) : null;

        return view('admin.serp-analysis.create', compact('sites', 'keywords', 'siteId', 'keywordId', 'keyword'));
    }

    /**
     * Analizar SERP para una keyword
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'keyword' => 'required|string|max:255',
            'keyword_id' => 'nullable|exists:keywords,id',
        ]);

        $site = Site::findOrFail($validated['site_id']);
        $keywordModel = $validated['keyword_id'] ? Keyword::find($validated['keyword_id']) : null;

        try {
            // Mostrar mensaje de que está procesando
            Log::info("Iniciando análisis de SERP para '{$validated['keyword']}' del sitio {$site->id}");

            $analysis = $this->serpService->analyzeSerp($site, $validated['keyword'], $keywordModel);

            Log::info("Análisis de SERP completado exitosamente");

            return redirect()->route('serp-analysis.show', $analysis)
                ->with('success', "Análisis de SERP completado para '{$validated['keyword']}'.");
        } catch (\Exception $e) {
            Log::error("Error en análisis de SERP: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al analizar SERP: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de un análisis
     */
    public function show(SerpAnalysis $serpAnalysis)
    {
        $serpAnalysis->load('site', 'keywordRelation');

        return view('admin.serp-analysis.show', compact('serpAnalysis'));
    }

    /**
     * Re-analizar SERP
     */
    public function reanalyze($serpAnalysis)
    {
        try {
            // Si viene como ID, buscar el modelo
            if (!($serpAnalysis instanceof SerpAnalysis)) {
                $serpAnalysis = SerpAnalysis::findOrFail($serpAnalysis);
            }

            $serpAnalysis->load('site', 'keywordRelation');
            $site = $serpAnalysis->site;
            $keywordModel = $serpAnalysis->keywordRelation;
            $keywordString = $serpAnalysis->getAttribute('keyword');

            $analysis = $this->serpService->analyzeSerp($site, $keywordString, $keywordModel);

            return redirect()->route('serp-analysis.show', $analysis->id)
                ->with('success', 'Análisis de SERP actualizado.');
        } catch (\Exception $e) {
            Log::error("Error al re-analizar SERP: " . $e->getMessage());
            return back()->with('error', 'Error al re-analizar SERP: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar análisis
     */
    public function destroy(SerpAnalysis $serpAnalysis)
    {
        $keyword = $serpAnalysis->getAttribute('keyword');
        $serpAnalysis->delete();

        return redirect()->route('serp-analysis.index')
            ->with('success', "Análisis de SERP para '{$keyword}' eliminado.");
    }

    /**
     * Analizar múltiples keywords desde tracking
     */
    public function analyzeFromKeywords(Request $request, Site $site)
    {
        $keywordIds = $request->get('keyword_ids', []);

        if (empty($keywordIds)) {
            return back()->with('error', 'Selecciona al menos una keyword para analizar.');
        }

        $keywords = Keyword::where('site_id', $site->id)
            ->whereIn('id', $keywordIds)
            ->get();

        if ($keywords->isEmpty()) {
            return back()->with('error', 'No se encontraron keywords válidas.');
        }

        try {
            $keywordStrings = $keywords->pluck('keyword')->toArray();
            $results = $this->serpService->analyzeMultipleKeywords($site, $keywordStrings);

            return redirect()->route('serp-analysis.index', ['site_id' => $site->id])
                ->with('success', "Se analizaron " . count($results) . " keywords.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al analizar keywords: ' . $e->getMessage());
        }
    }

    /**
     * Editar análisis (redirige a show)
     */
    public function edit(SerpAnalysis $serpAnalysis)
    {
        return redirect()->route('serp-analysis.show', $serpAnalysis);
    }

    /**
     * Actualizar análisis (redirige a show)
     */
    public function update(Request $request, SerpAnalysis $serpAnalysis)
    {
        return redirect()->route('serp-analysis.show', $serpAnalysis);
    }
}
