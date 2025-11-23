<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\Competitor;
use App\Models\Keyword;
use App\Models\KeywordCompetitorComparison;
use App\Models\SeoMetric;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompetitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.competitors.index')->only('index');
        $this->middleware('can:admin.competitors.create')->only('create', 'store');
        $this->middleware('can:admin.competitors.edit')->only('edit', 'update');
        $this->middleware('can:admin.competitors.show')->only('show', 'dashboard');
        $this->middleware('can:admin.competitors.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');

        $query = Competitor::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $competitors = $query->latest()->paginate(20);
        $sites = Site::active()->get();

        return view('admin.competitors.index', compact('competitors', 'sites', 'siteId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $siteId = $request->get('site_id');
        $sites = Site::active()->get();

        return view('admin.competitors.create', compact('sites', 'siteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'nombre' => 'required|string|max:255',
            'dominio_base' => 'required|string|max:255',
            'gsc_property' => 'nullable|string',
            'gsc_credentials' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verificar que no exista el competidor para este sitio
        $exists = Competitor::where('site_id', $validated['site_id'])
            ->where('dominio_base', $validated['dominio_base'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['dominio_base' => 'Este competidor ya está registrado para este sitio.']);
        }

        // Validar JSON de credenciales si se proporciona
        if (!empty($validated['gsc_credentials'])) {
            $decoded = json_decode($validated['gsc_credentials'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['gsc_credentials' => 'El JSON de credenciales no es válido.']);
            }
            $validated['gsc_credentials'] = $decoded;
        }

        Competitor::create($validated);

        return redirect()->route('competitors.index', ['site_id' => $validated['site_id']])
            ->with('success', 'Competidor agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competitor $competitor)
    {
        $competitor->load('site');

        // Obtener keywords del sitio para comparar
        $keywords = Keyword::where('site_id', $competitor->site_id)
            ->active()
            ->get();

        // Obtener comparaciones recientes
        $comparisons = KeywordCompetitorComparison::where('competitor_id', $competitor->id)
            ->with('keyword')
            ->whereDate('date', '>=', Carbon::now()->subDays(7))
            ->latest('date')
            ->get();

        // Estadísticas
        $totalKeywords = $keywords->count();
        $keywordsCompared = $comparisons->pluck('keyword_id')->unique()->count();
        $keywordsWhereCompetitorBetter = $comparisons->where('position_gap', '>', 0)->count();
        $keywordsWhereWeBetter = $comparisons->where('position_gap', '<', 0)->count();

        return view('admin.competitors.show', compact(
            'competitor',
            'keywords',
            'comparisons',
            'totalKeywords',
            'keywordsCompared',
            'keywordsWhereCompetitorBetter',
            'keywordsWhereWeBetter'
        ));
    }

    /**
     * Dashboard de competencia
     */
    public function dashboard(Request $request, Site $site)
    {
        $competitorId = $request->get('competitor_id');

        $competitors = $site->competitors()->active()->get();

        if ($competitorId) {
            $selectedCompetitor = Competitor::findOrFail($competitorId);
        } else {
            $selectedCompetitor = $competitors->first();
        }

        if (!$selectedCompetitor) {
            return redirect()->route('competitors.index', ['site_id' => $site->id])
                ->with('info', 'Agrega competidores para ver el análisis de competencia.');
        }

        // Obtener keywords del sitio
        $keywords = Keyword::where('site_id', $site->id)
            ->active()
            ->get();

        // Obtener comparaciones
        $comparisons = [];
        foreach ($keywords as $keyword) {
            $comparison = KeywordCompetitorComparison::where('keyword_id', $keyword->id)
                ->where('competitor_id', $selectedCompetitor->id)
                ->latest('date')
                ->first();

            if ($comparison) {
                $comparisons[] = [
                    'keyword' => $keyword,
                    'our_position' => $keyword->current_position,
                    'competitor_position' => $comparison->competitor_position,
                    'gap' => $comparison->position_gap,
                    'date' => $comparison->date,
                ];
            } else {
                // Si no hay comparación, solo mostrar nuestra posición
                $comparisons[] = [
                    'keyword' => $keyword,
                    'our_position' => $keyword->current_position,
                    'competitor_position' => null,
                    'gap' => null,
                    'date' => null,
                ];
            }
        }

        // Ordenar por gap (competidor mejor primero)
        usort($comparisons, function($a, $b) {
            if ($a['gap'] === null && $b['gap'] === null) return 0;
            if ($a['gap'] === null) return 1;
            if ($b['gap'] === null) return -1;
            return $b['gap'] <=> $a['gap']; // Mayor gap primero (competidor mejor)
        });

        // Identificar gaps (keywords donde competidor está mejor)
        $gaps = array_filter($comparisons, function($comp) {
            return $comp['gap'] !== null && $comp['gap'] > 0;
        });

        // Fecha por defecto para el formulario
        $defaultDate = Carbon::yesterday()->format('Y-m-d');

        return view('admin.competitors.dashboard', compact(
            'site',
            'competitors',
            'selectedCompetitor',
            'comparisons',
            'gaps',
            'defaultDate'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competitor $competitor)
    {
        $competitor->load('site');
        $sites = Site::active()->get();

        return view('admin.competitors.edit', compact('competitor', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competitor $competitor)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'nombre' => 'required|string|max:255',
            'dominio_base' => 'required|string|max:255',
            'gsc_property' => 'nullable|string',
            'gsc_credentials' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Verificar que no exista otro competidor igual para este sitio
        if ($competitor->dominio_base !== $validated['dominio_base'] || $competitor->site_id !== $validated['site_id']) {
            $exists = Competitor::where('site_id', $validated['site_id'])
                ->where('dominio_base', $validated['dominio_base'])
                ->where('id', '!=', $competitor->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->withErrors(['dominio_base' => 'Este competidor ya está registrado para este sitio.']);
            }
        }

        // Validar JSON de credenciales si se proporciona
        if (!empty($validated['gsc_credentials'])) {
            $decoded = json_decode($validated['gsc_credentials'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['gsc_credentials' => 'El JSON de credenciales no es válido.']);
            }
            $validated['gsc_credentials'] = $decoded;
        }

        $competitor->update($validated);

        return redirect()->route('competitors.index', ['site_id' => $competitor->site_id])
            ->with('success', 'Competidor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competitor $competitor)
    {
        $siteId = $competitor->site_id;
        $competitor->delete();

        return redirect()->route('competitors.index', ['site_id' => $siteId])
            ->with('success', 'Competidor eliminado exitosamente.');
    }

    /**
     * Comparar posiciones de keywords con competidor
     */
    public function compareKeywords(Request $request, Competitor $competitor)
    {
        $keywordIds = $request->get('keyword_ids', []);
        $date = $request->get('date', Carbon::yesterday()->format('Y-m-d'));

        if (empty($keywordIds)) {
            return back()->withErrors(['keyword_ids' => 'Selecciona al menos una keyword para comparar.']);
        }

        $keywords = Keyword::whereIn('id', $keywordIds)
            ->where('site_id', $competitor->site_id)
            ->get();

        $compared = 0;

        foreach ($keywords as $keyword) {
            // Obtener nuestra posición desde métricas
            $ourPosition = $keyword->getPositionFromMetrics($date);

            // Obtener posición del competidor (si tiene GSC configurado)
            $competitorPosition = null;
            if ($competitor->gsc_property && $competitor->gsc_credentials) {
                // Aquí podríamos usar el servicio GSC para obtener la posición del competidor
                // Por ahora, lo dejamos como null y el usuario puede ingresarlo manualmente
            }

            // Si tenemos ambas posiciones, calcular gap
            $gap = null;
            if ($ourPosition !== null && $competitorPosition !== null) {
                $gap = $ourPosition - $competitorPosition; // Positivo = competidor mejor, negativo = nosotros mejor
            }

            // Guardar o actualizar comparación
            KeywordCompetitorComparison::updateOrCreate(
                [
                    'keyword_id' => $keyword->id,
                    'competitor_id' => $competitor->id,
                    'date' => $date,
                ],
                [
                    'competitor_position' => $competitorPosition,
                    'position_gap' => $gap,
                ]
            );

            $compared++;
        }

        return back()->with('success', "Se compararon {$compared} keywords.");
    }

    /**
     * Actualizar posición manual de una keyword para un competidor
     */
    public function updatePosition(Request $request, Competitor $competitor)
    {
        $request->validate([
            'keyword_id' => 'required|exists:keywords,id',
            'competitor_position' => 'nullable|integer|min:1|max:100',
            'date' => 'nullable|date',
        ]);

        $keyword = Keyword::findOrFail($request->keyword_id);

        // Verificar que la keyword pertenezca al mismo sitio
        if ($keyword->site_id !== $competitor->site_id) {
            return response()->json(['error' => 'La keyword no pertenece al mismo sitio que el competidor.'], 400);
        }

        $date = $request->get('date', Carbon::yesterday()->format('Y-m-d'));
        $competitorPosition = $request->get('competitor_position');

        // Obtener nuestra posición
        $ourPosition = $keyword->current_position;

        // Calcular gap si tenemos ambas posiciones
        $gap = null;
        if ($ourPosition !== null && $competitorPosition !== null) {
            $gap = $ourPosition - $competitorPosition; // Positivo = competidor mejor, negativo = nosotros mejor
        }

        // Guardar o actualizar comparación
        $comparison = KeywordCompetitorComparison::updateOrCreate(
            [
                'keyword_id' => $keyword->id,
                'competitor_id' => $competitor->id,
                'date' => $date,
            ],
            [
                'competitor_position' => $competitorPosition,
                'position_gap' => $gap,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Posición actualizada correctamente.',
            'comparison' => [
                'our_position' => $ourPosition,
                'competitor_position' => $competitorPosition,
                'gap' => $gap,
            ]
        ]);
    }

    /**
     * Actualizar múltiples posiciones a la vez
     */
    public function updatePositions(Request $request, Competitor $competitor)
    {
        $request->validate([
            'positions' => 'required|array',
            'positions.*.keyword_id' => 'required|exists:keywords,id',
            'positions.*.competitor_position' => 'nullable|integer|min:1|max:100',
            'date' => 'nullable|date',
        ]);

        $date = $request->get('date', Carbon::yesterday()->format('Y-m-d'));
        $updated = 0;

        foreach ($request->positions as $positionData) {
            $keyword = Keyword::findOrFail($positionData['keyword_id']);

            // Verificar que la keyword pertenezca al mismo sitio
            if ($keyword->site_id !== $competitor->site_id) {
                continue;
            }

            $competitorPosition = isset($positionData['competitor_position']) && $positionData['competitor_position'] !== ''
                ? (int)$positionData['competitor_position']
                : null;

            // Obtener nuestra posición
            $ourPosition = $keyword->current_position;

            // Calcular gap si tenemos ambas posiciones
            $gap = null;
            if ($ourPosition !== null && $competitorPosition !== null) {
                $gap = $ourPosition - $competitorPosition;
            }

            // Guardar o actualizar comparación
            KeywordCompetitorComparison::updateOrCreate(
                [
                    'keyword_id' => $keyword->id,
                    'competitor_id' => $competitor->id,
                    'date' => $date,
                ],
                [
                    'competitor_position' => $competitorPosition,
                    'position_gap' => $gap,
                ]
            );

            $updated++;
        }

        return back()->with('success', "Se actualizaron {$updated} posiciones del competidor.");
    }
}
