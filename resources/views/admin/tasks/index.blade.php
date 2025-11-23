@extends('adminlte::page')

@section('title', 'Tareas SEO')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Tareas SEO</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('tasks.kanban', ['site_id' => $siteId]) }}" class="btn btn-primary">
                <i class="fas fa-th"></i> Vista Kanban
            </a>
            <a href="{{ route('tasks.create', ['site_id' => $siteId]) }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
        </div>
    </div>
@stop

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select name="site_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los sitios</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                    {{ $site->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas las prioridades</option>
                            <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="critical" {{ $priority == 'critical' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Sitio</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        @php
                            $priorityBadge = $task->getPriorityBadge();
                            $statusBadge = $task->getStatusBadge();
                            $isOverdue = $task->isOverdue();
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                            <td>{{ $task->id }}</td>
                            <td>
                                <strong>{{ $task->title }}</strong>
                                @if($task->url)
                                    <br><small class="text-muted">
                                        <a href="{{ $task->url }}" target="_blank">{{ Str::limit($task->url, 40) }}</a>
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sites.show', $task->site) }}">
                                    {{ $task->site->nombre }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-{{ $priorityBadge['class'] }}">
                                    {{ $priorityBadge['text'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $statusBadge['class'] }}">
                                    {{ $statusBadge['text'] }}
                                </span>
                            </td>
                            <td>
                                @if($task->assignee)
                                    {{ $task->assignee->name }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($task->due_date)
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if($isOverdue)
                                        <span class="badge badge-danger">Vencida</span>
                                    @endif
                                @else
                                    <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay tareas registradas.
                                <a href="{{ route('tasks.create', ['site_id' => $siteId]) }}">Crear primera tarea</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tasks->hasPages())
            <div class="card-footer">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@stop

