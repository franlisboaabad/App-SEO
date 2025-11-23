<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\Keyword;
use App\Models\SeoMetric;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KeywordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.keywords.index')->only('index');
        $this->middleware('can:admin.keywords.create')->only('create', 'store');
        $this->middleware('can:admin.keywords.edit')->only('edit', 'update');
        $this->middleware('can:admin.keywords.show')->only('show', 'dashboard');
        $this->middleware('can:admin.keywords.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');

        $query = Keyword::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $keywords = $query->latest()->paginate(20);
        $sites = Site::active()->get();

        return view('admin.keywords.index', compact('keywords', 'sites', 'siteId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $siteId = $request->get('site_id');
        $sites = Site::active()->get();

        return view('admin.keywords.create', compact('sites', 'siteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'keyword' => 'required|string|max:255',
            'target_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        // Verificar que no exista la keyword para este sitio
        $exists = Keyword::where('site_id', $validated['site_id'])
            ->where('keyword', $validated['keyword'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['keyword' => 'Esta keyword ya está siendo seguida para este sitio.']);
        }

        // Obtener posición actual desde métricas
        $currentPosition = $this->getCurrentPosition($validated['site_id'], $validated['keyword']);

        Keyword::create([
            'site_id' => $validated['site_id'],
            'keyword' => $validated['keyword'],
            'target_url' => $validated['target_url'] ?? null,
            'current_position' => $currentPosition,
            'last_checked' => Carbon::now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('keywords.index', ['site_id' => $validated['site_id']])
            ->with('success', 'Keyword agregada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Keyword $keyword)
    {
        $keyword->load('site');

        // Obtener historial de posiciones
        $history = $keyword->getPositionHistory(30);

        // Obtener comparaciones
        $today = Carbon::yesterday();
        $yesterday = Carbon::yesterday()->subDay();
        $weekAgo = Carbon::yesterday()->subDays(7);

        $positionToday = $keyword->getPositionFromMetrics($today);
        $positionYesterday = $keyword->getPositionFromMetrics($yesterday);
        $positionWeekAgo = $keyword->getPositionFromMetrics($weekAgo);

        // Obtener métricas de la keyword
        $metrics = SeoMetric::where('site_id', $keyword->site_id)
            ->where('keyword', $keyword->keyword)
            ->whereDate('date', '>=', $weekAgo)
            ->selectRaw('DATE(date) as date, AVG(position) as position, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr')
            ->groupByRaw('DATE(date)')
            ->orderBy('date')
            ->get();

        return view('admin.keywords.show', compact('keyword', 'history', 'positionToday', 'positionYesterday', 'positionWeekAgo', 'metrics'));
    }

    /**
     * Dashboard de keyword con gráfico
     */
    public function dashboard(Keyword $keyword)
    {
        $keyword->load('site');

        // Obtener historial de 30 días
        $history = $keyword->getPositionHistory(30);

        // Preparar datos para el gráfico
        $chartData = [
            'labels' => $history->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray(),
            'positions' => $history->pluck('position')->toArray(),
            'clicks' => $history->pluck('clicks')->toArray(),
            'impressions' => $history->pluck('impressions')->toArray(),
        ];

        // Comparaciones
        $today = Carbon::yesterday();
        $yesterday = Carbon::yesterday()->subDay();
        $weekAgo = Carbon::yesterday()->subDays(7);

        $positionToday = $keyword->getPositionFromMetrics($today);
        $positionYesterday = $keyword->getPositionFromMetrics($yesterday);
        $positionWeekAgo = $keyword->getPositionFromMetrics($weekAgo);

        return view('admin.keywords.dashboard', compact('keyword', 'chartData', 'positionToday', 'positionYesterday', 'positionWeekAgo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keyword $keyword)
    {
        $keyword->load('site');
        $sites = Site::active()->get();

        return view('admin.keywords.edit', compact('keyword', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keyword $keyword)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'keyword' => 'required|string|max:255',
            'target_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Verificar que no exista otra keyword igual para este sitio
        if ($keyword->keyword !== $validated['keyword'] || $keyword->site_id !== $validated['site_id']) {
            $exists = Keyword::where('site_id', $validated['site_id'])
                ->where('keyword', $validated['keyword'])
                ->where('id', '!=', $keyword->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->withErrors(['keyword' => 'Esta keyword ya está siendo seguida para este sitio.']);
            }
        }

        $keyword->update($validated);

        return redirect()->route('keywords.index', ['site_id' => $keyword->site_id])
            ->with('success', 'Keyword actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keyword $keyword)
    {
        $siteId = $keyword->site_id;
        $keyword->delete();

        return redirect()->route('keywords.index', ['site_id' => $siteId])
            ->with('success', 'Keyword eliminada exitosamente.');
    }

    /**
     * Actualizar posiciones de todas las keywords
     */
    public function updatePositions(Request $request)
    {
        $siteId = $request->get('site_id');

        $query = Keyword::active();
        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $keywords = $query->get();
        $updated = 0;

        $alertService = new AlertService();

        foreach ($keywords as $keyword) {
            $oldPosition = $keyword->current_position;
            $newPosition = $this->getCurrentPosition($keyword->site_id, $keyword->keyword);

            if ($newPosition !== null) {
                $keyword->update([
                    'previous_position' => $oldPosition,
                    'current_position' => $newPosition,
                    'last_checked' => Carbon::now(),
                ]);

                // Crear alerta si hay pérdida significativa de posición
                if ($oldPosition !== null && ($newPosition - $oldPosition) > 5) {
                    $alertService->createPositionAlert(
                        $keyword->site,
                        $keyword->keyword,
                        $keyword->target_url,
                        $oldPosition,
                        $newPosition
                    );
                }

                $updated++;
            }
        }

        return back()->with('success', "Se actualizaron {$updated} keywords.");
    }

    /**
     * Obtener posición actual desde métricas
     */
    private function getCurrentPosition($siteId, $keyword, $date = null)
    {
        $date = $date ?: Carbon::yesterday();

        $metric = SeoMetric::where('site_id', $siteId)
            ->where('keyword', $keyword)
            ->whereDate('date', $date)
            ->selectRaw('AVG(position) as avg_position')
            ->first();

        return $metric && $metric->avg_position ? round($metric->avg_position, 1) : null;
    }
}
