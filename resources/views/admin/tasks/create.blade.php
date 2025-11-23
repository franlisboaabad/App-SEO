@extends('adminlte::page')

@section('title', 'Nueva Tarea SEO')

@section('content_header')
    <h1>Crear Nueva Tarea SEO</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="site_id">Sitio *</label>
                    <select name="site_id" id="site_id" class="form-control @error('site_id') is-invalid @enderror" required>
                        <option value="">Seleccione un sitio</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $siteId) == $site->id ? 'selected' : '' }}>
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
                           value="{{ old('title') }}"
                           placeholder="Ej: Corregir meta description de página principal"
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
                              rows="4">{{ old('description') }}</textarea>
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
                           value="{{ old('url') }}"
                           placeholder="https://ejemplo.com/pagina">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority">Prioridad *</label>
                            <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Media</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Crítica</option>
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
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
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
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($auditId)
                    <input type="hidden" name="seo_audit_id" value="{{ $auditId }}">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Esta tarea será asociada con una auditoría SEO.
                    </div>
                @endif

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Tarea
                    </button>
                    <a href="{{ route('tasks.kanban', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

