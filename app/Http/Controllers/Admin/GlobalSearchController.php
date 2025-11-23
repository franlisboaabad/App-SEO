<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keyword;
use App\Models\Site;
use App\Models\SeoTask;
use App\Models\AuditResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    /**
     * Buscar en todos los recursos
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query) || strlen($query) < 2) {
            return view('admin.global-search.results', [
                'query' => $query,
                'results' => [
                    'keywords' => collect(),
                    'sites' => collect(),
                    'tasks' => collect(),
                    'urls' => collect(),
                ],
                'total' => 0,
            ]);
        }

        $results = [
            'keywords' => $this->searchKeywords($query),
            'sites' => $this->searchSites($query),
            'tasks' => $this->searchTasks($query),
            'urls' => $this->searchUrls($query),
        ];

        $total = collect($results)->sum(fn($collection) => $collection->count());

        return view('admin.global-search.results', compact('query', 'results', 'total'));
    }

    /**
     * Autocompletado para búsqueda
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Keywords
        $keywords = Keyword::where('keyword', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit(5)
            ->get(['id', 'keyword', 'site_id']);

        foreach ($keywords as $keyword) {
            $suggestions[] = [
                'type' => 'keyword',
                'text' => $keyword->keyword,
                'url' => route('keywords.index', ['site_id' => $keyword->site_id, 'search' => $keyword->keyword]),
                'icon' => 'fas fa-key',
            ];
        }

        // Sitios
        $sites = Site::where('nombre', 'like', "%{$query}%")
            ->orWhere('dominio_base', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'nombre', 'dominio_base']);

        foreach ($sites as $site) {
            $suggestions[] = [
                'type' => 'site',
                'text' => $site->nombre . ' (' . $site->dominio_base . ')',
                'url' => route('sites.show', $site->id),
                'icon' => 'fas fa-globe',
            ];
        }

        // Tareas
        $tasks = SeoTask::where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'title', 'site_id']);

        foreach ($tasks as $task) {
            $suggestions[] = [
                'type' => 'task',
                'text' => $task->title,
                'url' => route('sites.show', $task->site_id) . '#tasks',
                'icon' => 'fas fa-tasks',
            ];
        }

        return response()->json($suggestions);
    }

    /**
     * Buscar keywords
     */
    private function searchKeywords($query)
    {
        return Keyword::where('keyword', 'like', "%{$query}%")
            ->with('site')
            ->orderBy('keyword')
            ->limit(20)
            ->get();
    }

    /**
     * Buscar sitios
     */
    private function searchSites($query)
    {
        return Site::where('nombre', 'like', "%{$query}%")
            ->orWhere('dominio_base', 'like', "%{$query}%")
            ->orderBy('nombre')
            ->limit(20)
            ->get();
    }

    /**
     * Buscar tareas
     */
    private function searchTasks($query)
    {
        return SeoTask::where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('site')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Buscar URLs (en audit results y keywords)
     */
    private function searchUrls($query)
    {
        $urls = collect();

        // URLs en audit results
        $auditUrls = AuditResult::where('url', 'like', "%{$query}%")
            ->with('audit.site')
            ->limit(10)
            ->get()
            ->map(function ($result) {
                return [
                    'url' => $result->url,
                    'site' => $result->audit ? ($result->audit->site ?? null) : null,
                    'type' => 'audit',
                    'created_at' => $result->created_at,
                ];
            });

        $urls = $urls->merge($auditUrls);

        // URLs en keywords (target_url)
        $keywordUrls = Keyword::where('target_url', 'like', "%{$query}%")
            ->with('site')
            ->limit(10)
            ->get()
            ->map(function ($keyword) {
                return [
                    'url' => $keyword->target_url,
                    'site' => $keyword->site,
                    'type' => 'keyword',
                    'created_at' => $keyword->created_at,
                ];
            });

        $urls = $urls->merge($keywordUrls);

        return $urls->unique('url')->take(20);
    }
}

