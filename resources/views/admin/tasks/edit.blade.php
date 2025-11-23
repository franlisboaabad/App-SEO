@extends('adminlte::page')

@section('title', 'Editar Tarea SEO')

@section('content_header')
    <h1>Editar Tarea: {{ $task->title }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="site_id">Sitio *</label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $task->site_id) == $site->id ? 'selected' : '' }}>
                                {{ $site->nombre }} ({{ $site->dominio_base }})
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title">Título *</label>
                    <input type="text"
                           name="title"
                           id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $task->title) }}"
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea name="description"
                              id="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="4">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="url">URL Relacionada (Opcional)</label>
                    <input type="url"
                           name="url"
                           id="url"
                           class="form-control @error('url') is-invalid @enderror"
                           value="{{ old('url', $task->url) }}">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority">Prioridad *</label>
                            <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Baja</option>
                                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Media</option>
                                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="critical" {{ old('priority', $task->priority) == 'critical' ? 'selected' : '' }}>Crítica</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Estado *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="assigned_to">Asignar a (Opcional)</label>
                            <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                                <option value="">Sin asignar</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="due_date">Fecha Límite (Opcional)</label>
                            <input type="date"
                                   name="due_date"
                                   id="due_date"
                                   class="form-control @error('due_date') is-invalid @enderror"
                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Tarea
                    </button>
                    <a href="{{ route('tasks.kanban', ['site_id' => $task->site_id]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

