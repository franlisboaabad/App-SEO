<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\SeoTask;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SeoTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.tasks.index')->only('index', 'kanban');
        $this->middleware('can:admin.tasks.create')->only('create', 'store');
        $this->middleware('can:admin.tasks.edit')->only('edit', 'update', 'updateStatus');
        $this->middleware('can:admin.tasks.show')->only('show');
        $this->middleware('can:admin.tasks.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');
        $status = $request->get('status');
        $priority = $request->get('priority');

        $query = SeoTask::with('site', 'assignee', 'creator');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $tasks = $query->latest()->paginate(20);
        $sites = Site::active()->get();

        return view('admin.tasks.index', compact('tasks', 'sites', 'siteId', 'status', 'priority'));
    }

    /**
     * Vista Kanban
     */
    public function kanban(Request $request)
    {
        $siteId = $request->get('site_id');

        $query = SeoTask::with('site', 'assignee', 'creator')
            ->whereIn('status', ['pending', 'in_progress', 'completed']);

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $tasks = $query->get();

        // Agrupar por estado
        $pending = $tasks->where('status', 'pending')->sortBy('priority');
        $inProgress = $tasks->where('status', 'in_progress')->sortBy('priority');
        $completed = $tasks->where('status', 'completed')->take(20); // Solo últimas 20 completadas

        $sites = Site::active()->get();

        return view('admin.tasks.kanban', compact('pending', 'inProgress', 'completed', 'sites', 'siteId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $siteId = $request->get('site_id');
        $auditId = $request->get('audit_id');

        $sites = Site::active()->get();
        $users = User::all();

        return view('admin.tasks.create', compact('sites', 'users', 'siteId', 'auditId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'seo_audit_id' => 'nullable|exists:seo_audits,id',
        ]);

        $validated['created_by'] = auth()->id();

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = Carbon::now();
        }

        SeoTask::create($validated);

        return redirect()->route('tasks.kanban', ['site_id' => $validated['site_id']])
            ->with('success', 'Tarea creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeoTask $task)
    {
        $task->load('site', 'assignee', 'creator', 'seoAudit');

        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeoTask $task)
    {
        $task->load('site');
        $sites = Site::active()->get();
        $users = User::all();

        return view('admin.tasks.edit', compact('task', 'sites', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoTask $task)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        // Si cambia a completada, agregar fecha de completado
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = Carbon::now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.kanban', ['site_id' => $task->site_id])
            ->with('success', 'Tarea actualizada exitosamente.');
    }

    /**
     * Actualizar solo el estado (desde Kanban)
     */
    public function updateStatus(Request $request, SeoTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = Carbon::now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeoTask $task)
    {
        $siteId = $task->site_id;
        $task->delete();

        return redirect()->route('tasks.kanban', ['site_id' => $siteId])
            ->with('success', 'Tarea eliminada exitosamente.');
    }
}
