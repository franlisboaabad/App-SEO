@extends('adminlte::page')

@section('title', 'Planificador de Tareas SEO')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Planificador de Tareas SEO</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('tasks.create', ['site_id' => $siteId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
            <a href="{{ route('tasks.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Vista Lista
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

    <!-- Filtro por sitio -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.kanban') }}">
                <div class="row">
                    <div class="col-md-4">
                        <select name="site_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los sitios</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                    {{ $site->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="row">
        <!-- Columna: Pendiente -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Pendiente
                        <span class="badge badge-warning ml-2">{{ $pending->count() }}</span>
                    </h3>
                </div>
                <div class="card-body" style="min-height: 500px; max-height: 800px; overflow-y: auto;">
                    @forelse($pending as $task)
                        @include('admin.tasks.partials.task-card', ['task' => $task])
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No hay tareas pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna: En Progreso -->
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-spinner"></i> En Progreso
                        <span class="badge badge-info ml-2">{{ $inProgress->count() }}</span>
                    </h3>
                </div>
                <div class="card-body" style="min-height: 500px; max-height: 800px; overflow-y: auto;">
                    @forelse($inProgress as $task)
                        @include('admin.tasks.partials.task-card', ['task' => $task])
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tasks fa-2x mb-2"></i>
                            <p>No hay tareas en progreso</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna: Completadas -->
        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle"></i> Completadas
                        <span class="badge badge-success ml-2">{{ $completed->count() }}</span>
                    </h3>
                </div>
                <div class="card-body" style="min-height: 500px; max-height: 800px; overflow-y: auto;">
                    @forelse($completed as $task)
                        @include('admin.tasks.partials.task-card', ['task' => $task])
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check fa-2x mb-2"></i>
                            <p>No hay tareas completadas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .task-card {
            cursor: move;
            margin-bottom: 10px;
        }
        .task-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .task-priority-low { border-left: 3px solid #6c757d; }
        .task-priority-medium { border-left: 3px solid #17a2b8; }
        .task-priority-high { border-left: 3px solid #ffc107; }
        .task-priority-critical { border-left: 3px solid #dc3545; }
        .task-overdue {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107 !important;
        }
    </style>
@stop

@section('js')
    <script>
        // Cambiar estado de tarea al hacer clic en los botones
        document.querySelectorAll('.change-status-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.dataset.taskId;
                const newStatus = this.dataset.status;

                if (confirm('¿Cambiar el estado de esta tarea?')) {
                    fetch(`/admin/tasks/${taskId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error al actualizar la tarea');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar la tarea');
                    });
                }
            });
        });
    </script>
@stop

