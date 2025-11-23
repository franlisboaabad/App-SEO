@extends('adminlte::page')

@section('title', 'Alertas SEO')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-bell"></i> Alertas SEO</h1>
        </div>
        <div class="col-md text-right">
            <form action="{{ route('alerts.detect-changes') }}" method="POST" class="d-inline">
                @csrf
                @if($siteId)
                    <input type="hidden" name="site_id" value="{{ $siteId }}">
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Detectar Cambios
                </button>
            </form>
            <form action="{{ route('alerts.mark-all-as-read') }}" method="POST" class="d-inline">
                @csrf
                @if($siteId)
                    <input type="hidden" name="site_id" value="{{ $siteId }}">
                @endif
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-check-double"></i> Marcar Todas como Leídas
                </button>
            </form>
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

    <!-- Estadísticas -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-bell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Alertas</span>
                    <span class="info-box-number">{{ $totalAlerts }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-envelope"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">No Leídas</span>
                    <span class="info-box-number">{{ $unreadAlerts }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Críticas</span>
                    <span class="info-box-number">{{ $criticalAlerts }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('alerts.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <label for="site_id" class="mr-2">Sitio:</label>
                    <select name="site_id" id="site_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                {{ $site->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="type" class="mr-2">Tipo:</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">Todos</option>
                        <option value="position" {{ $type == 'position' ? 'selected' : '' }}>Posición</option>
                        <option value="traffic" {{ $type == 'traffic' ? 'selected' : '' }}>Tráfico</option>
                        <option value="error" {{ $type == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="content" {{ $type == 'content' ? 'selected' : '' }}>Contenido</option>
                        <option value="performance" {{ $type == 'performance' ? 'selected' : '' }}>Rendimiento</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="severity" class="mr-2">Severidad:</label>
                    <select name="severity" id="severity" class="form-control">
                        <option value="">Todas</option>
                        <option value="critical" {{ $severity == 'critical' ? 'selected' : '' }}>Crítica</option>
                        <option value="warning" {{ $severity == 'warning' ? 'selected' : '' }}>Advertencia</option>
                        <option value="info" {{ $severity == 'info' ? 'selected' : '' }}>Info</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <div class="form-check">
                        <input type="checkbox" name="unread_only" id="unread_only" class="form-check-input" value="1" {{ $unreadOnly ? 'checked' : '' }}>
                        <label class="form-check-label" for="unread_only">Solo no leídas</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Alertas -->
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>Fecha</th>
                        <th>Sitio</th>
                        <th>Tipo</th>
                        <th>Severidad</th>
                        <th>Título</th>
                        <th>Mensaje</th>
                        <th>URL</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alerts as $alert)
                        <tr class="{{ !$alert->is_read ? 'table-warning' : '' }}">
                            <td>
                                <input type="checkbox" class="alert-checkbox" value="{{ $alert->id }}">
                            </td>
                            <td>{{ $alert->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('sites.show', $alert->site) }}">
                                    {{ $alert->site->nombre }}
                                </a>
                            </td>
                            <td>{!! $alert->type_badge !!}</td>
                            <td>{!! $alert->severity_badge !!}</td>
                            <td><strong>{{ $alert->title }}</strong></td>
                            <td>{{ Str::limit($alert->message, 80) }}</td>
                            <td>
                                @if($alert->url)
                                    <a href="{{ $alert->url }}" target="_blank" rel="noopener">
                                        {{ Str::limit($alert->url, 40) }}
                                        <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($alert->is_read)
                                    <span class="badge badge-success">Leída</span>
                                @else
                                    <span class="badge badge-warning">No leída</span>
                                @endif
                                @if($alert->resolved_at)
                                    <br><small class="text-muted">Resuelta: {{ $alert->resolved_at->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(!$alert->is_read)
                                        <form action="{{ route('alerts.mark-as-read', $alert) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info" title="Marcar como leída">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$alert->resolved_at)
                                        <form action="{{ route('alerts.mark-as-resolved', $alert) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Marcar como resuelta">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No hay alertas que mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($alerts->hasPages())
            <div class="card-footer">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        // Select all checkbox
        $('#select-all').on('change', function() {
            $('.alert-checkbox').prop('checked', $(this).prop('checked'));
        });
    </script>
@stop

