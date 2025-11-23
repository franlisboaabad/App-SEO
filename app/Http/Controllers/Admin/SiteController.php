<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\SeoMetric;
use App\Models\SeoAudit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.sites.index')->only('index');
        $this->middleware('can:admin.sites.create')->only('create', 'store');
        $this->middleware('can:admin.sites.edit')->only('edit', 'update');
        $this->middleware('can:admin.sites.show')->only('show');
        $this->middleware('can:admin.sites.dashboard')->only('dashboard');
        $this->middleware('can:admin.sites.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sites = Site::latest()->get();
        return view('admin.sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.sites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dominio_base' => 'required|string|max:255|unique:sites,dominio_base',
            'gsc_property' => 'nullable|string|max:255',
            'gsc_credentials' => 'nullable|json',
            'estado' => 'boolean',
        ]);

        // Si viene como JSON string, convertirlo a array
        if (isset($validated['gsc_credentials']) && is_string($validated['gsc_credentials'])) {
            $validated['gsc_credentials'] = json_decode($validated['gsc_credentials'], true);
        }

        $validated['estado'] = $request->has('estado') ? true : false;

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Sitio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        return view('admin.sites.show', compact('site'));
    }

    /**
     * Dashboard SEO del sitio
     *
     * @param  \App\Models\Site  $site
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Site $site, Request $request)
    {
        // Rango de fechas (por defecto últimos 30 días)
        $endDate = $request->get('end_date', Carbon::yesterday()->format('Y-m-d'));
        $startDate = $request->get('start_date', Carbon::parse($endDate)->subDays(30)->format('Y-m-d'));
        $period = $request->get('period', '30'); // 7, 30, 90 días

        // Métricas agregadas por fecha
        $dailyMetrics = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('DATE(date) as date, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->groupByRaw('DATE(date)')
            ->orderByRaw('DATE(date)')
            ->get();

        // Top URLs por clics
        $topUrls = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('url, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->groupBy('url')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        // Top Keywords por clics
        $topKeywords = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('keyword, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->whereNotNull('keyword')
            ->groupBy('keyword')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        // Resumen total
        $summary = SeoMetric::forSite($site->id)
            ->dateRange($startDate, $endDate)
            ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->first();

        // Comparación con período anterior
        $previousStartDate = Carbon::parse($startDate)->subDays(Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) + 1)->format('Y-m-d');
        $previousEndDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');

        $previousSummary = SeoMetric::forSite($site->id)
            ->dateRange($previousStartDate, $previousEndDate)
            ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->first();

        // Auditorías recientes con errores
        $recentAudits = $site->seoAudits()
            ->with('result')
            ->completed()
            ->latest()
            ->limit(5)
            ->get();

        // Páginas con más errores SEO
        $pagesWithErrors = SeoAudit::where('site_id', $site->id)
            ->completed()
            ->with('result')
            ->get()
            ->filter(function ($audit) {
                return $audit->result && count($audit->result->errors ?? []) > 0;
            })
            ->sortByDesc(function ($audit) {
                return count($audit->result->errors ?? []);
            })
            ->take(5);

        return view('admin.sites.dashboard', compact(
            'site',
            'dailyMetrics',
            'topUrls',
            'topKeywords',
            'summary',
            'previousSummary',
            'recentAudits',
            'pagesWithErrors',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dominio_base' => 'required|string|max:255|unique:sites,dominio_base,' . $site->id,
            'gsc_property' => 'nullable|string|max:255',
            'gsc_credentials' => 'nullable|json',
            'estado' => 'boolean',
        ]);

        // Si viene como JSON string, convertirlo a array
        if (isset($validated['gsc_credentials']) && is_string($validated['gsc_credentials'])) {
            $validated['gsc_credentials'] = json_decode($validated['gsc_credentials'], true);
        }

        $validated['estado'] = $request->has('estado') ? true : false;

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Sitio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Sitio eliminado exitosamente.');
    }
}
