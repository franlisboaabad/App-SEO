@extends('adminlte::page')

@section('title', 'Búsqueda Global')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-search"></i> Búsqueda Global</h1>
        </div>
    </div>
@stop

@section('content')
    <!-- Formulario de Búsqueda -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('global-search.search') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <input type="text"
                           name="q"
                           class="form-control form-control-lg"
                           placeholder="Buscar keywords, sitios, URLs, tareas..."
                           value="{{ $query }}"
                           id="global-search-input"
                           autocomplete="off">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div id="autocomplete-results" class="position-absolute w-100 mt-1" style="z-index: 1000; display: none;">
                    <div class="list-group shadow-lg">
                        <!-- Los resultados se cargarán aquí via AJAX -->
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(empty($query) || strlen($query) < 2)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Ingresa al menos 2 caracteres para buscar.
        </div>
    @elseif($total == 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No se encontraron resultados para "<strong>{{ $query }}</strong>".
        </div>
    @else
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Se encontraron <strong>{{ $total }}</strong> resultado(s) para "<strong>{{ $query }}</strong>".
        </div>

        <!-- Resultados: Keywords -->
        @if($results['keywords']->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Keywords ({{ $results['keywords']->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Keyword</th>
                                    <th>Sitio</th>
                                    <th>Posición</th>
                                    <th>URL Objetivo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results['keywords'] as $keyword)
                                    <tr>
                                        <td><strong>{{ $keyword->keyword }}</strong></td>
                                        <td>
                                            @if($keyword->site)
                                                <a href="{{ route('sites.show', $keyword->site->id) }}">
                                                    {{ $keyword->site->nombre }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($keyword->current_position)
                                                <span class="badge badge-info">Posición {{ $keyword->current_position }}</span>
                                            @else
                                                <span class="badge badge-secondary">No rankea</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($keyword->target_url)
                                                <a href="{{ $keyword->target_url }}" target="_blank" rel="noopener">
                                                    {{ Str::limit($keyword->target_url, 50) }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('keywords.index', ['site_id' => $keyword->site_id, 'search' => $keyword->keyword]) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Resultados: Sitios -->
        @if($results['sites']->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i> Sitios Web ({{ $results['sites']->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Dominio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results['sites'] as $site)
                                    <tr>
                                        <td><strong>{{ $site->nombre }}</strong></td>
                                        <td>{{ $site->dominio_base }}</td>
                                        <td>
                                            @if($site->estado)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('sites.show', $site->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Resultados: Tareas -->
        @if($results['tasks']->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Tareas SEO ({{ $results['tasks']->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Sitio</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results['tasks'] as $task)
                                    <tr>
                                        <td><strong>{{ $task->title }}</strong></td>
                                        <td>
                                            @if($task->site)
                                                <a href="{{ route('sites.show', $task->site->id) }}">
                                                    {{ $task->site->nombre }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $priorityBadge = $task->getPriorityBadge();
                                            @endphp
                                            <span class="badge badge-{{ $priorityBadge['class'] }}">
                                                {{ $priorityBadge['text'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = $task->getStatusBadge();
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge['class'] }}">
                                                {{ $statusBadge['text'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('sites.show', $task->site_id) }}#tasks" class="btn btn-sm btn-warning">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Resultados: URLs -->
        @if($results['urls']->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i> URLs ({{ $results['urls']->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>Sitio</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results['urls'] as $urlData)
                                    <tr>
                                        <td>
                                            <a href="{{ $urlData['url'] }}" target="_blank" rel="noopener">
                                                {{ Str::limit($urlData['url'], 80) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($urlData['site'])
                                                <a href="{{ route('sites.show', $urlData['site']->id) }}">
                                                    {{ $urlData['site']->nombre }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($urlData['type'] == 'audit')
                                                <span class="badge badge-primary">Auditoría</span>
                                            @else
                                                <span class="badge badge-info">Keyword</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($urlData['site'])
                                                <a href="{{ route('sites.show', $urlData['site']->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Sitio
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
@stop

@section('js')
<script>
$(document).ready(function() {
    let searchTimeout;
    const $input = $('#global-search-input');
    const $results = $('#autocomplete-results');
    const $form = $input.closest('form');

    // Autocompletado mientras escribe
    $input.on('input', function() {
        const query = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $results.hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("global-search.autocomplete") }}',
                method: 'GET',
                data: { q: query },
                success: function(data) {
                    if (data.length === 0) {
                        $results.hide();
                        return;
                    }

                    let html = '';
                    data.forEach(function(item) {
                        html += `
                            <a href="${item.url}" class="list-group-item list-group-item-action">
                                <i class="${item.icon} mr-2"></i>
                                <strong>${item.text}</strong>
                                <small class="text-muted ml-2">(${item.type})</small>
                            </a>
                        `;
                    });

                    $results.find('.list-group').html(html);
                    $results.show();
                },
                error: function() {
                    $results.hide();
                }
            });
        }, 300);
    });

    // Ocultar resultados al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#global-search-input, #autocomplete-results').length) {
            $results.hide();
        }
    });

    // Enviar formulario al presionar Enter
    $input.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $form.submit();
        }
    });
});
</script>
@stop

