@extends('adminlte::page')

@section('title', 'Investigación de Keywords')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-search"></i> Investigación de Keywords</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('keyword-research.export', ['site_id' => $siteId]) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            @if($siteId)
                @php $site = \App\Models\Site::find($siteId); @endphp
                @if($site)
                    <form action="{{ route('keyword-research.assign-clusters', $site) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-layer-group"></i> Asignar Clusters
                        </button>
                    </form>
                    <a href="{{ route('keyword-research.clusters', ['site_id' => $siteId]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-sitemap"></i> Ver Clusters
                    </a>
                @endif
            @endif
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

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Herramientas de Búsqueda -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-tools"></i> Herramientas de Búsqueda</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Desde Google Search Console</h5>
                    <p class="text-muted">Obtiene keywords que ya están rankeando pero no están siendo trackeadas</p>
                    <form action="{{ route('keyword-research.search-gsc', $siteId ? ['site' => $siteId] : []) }}" method="POST" class="d-inline">
                        @csrf
                        @if($siteId)
                            <input type="hidden" name="site_id" value="{{ $siteId }}">
                        @endif
                        <div class="input-group mb-2">
                            <select name="site" class="form-control" required>
                                <option value="">Seleccione un sitio</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                        {{ $site->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar desde GSC
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h5>Google Autocomplete</h5>
                    <p class="text-muted">Obtiene sugerencias de búsqueda relacionadas</p>
                    <form action="{{ route('keyword-research.search-related') }}" method="POST" class="d-inline">
                        @csrf
                        @if($siteId)
                            <input type="hidden" name="site_id" value="{{ $siteId }}">
                        @endif
                        <div class="input-group mb-2">
                            <select name="site" class="form-control" required>
                                <option value="">Seleccione un sitio</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                        {{ $site->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <input type="text" name="seed_keyword" class="form-control" placeholder="Ej: hoteles en lima" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-lightbulb"></i> Buscar Sugerencias
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-key"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Keywords</span>
                    <span class="info-box-number">{{ $totalKeywords }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-eye-slash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">No Trackeadas</span>
                    <span class="info-box-number">{{ $untrackedCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('keyword-research.index') }}" class="form-inline">
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
                    <label for="source" class="mr-2">Fuente:</label>
                    <select name="source" id="source" class="form-control">
                        <option value="">Todas</option>
                        <option value="gsc" {{ $source == 'gsc' ? 'selected' : '' }}>Google Search Console</option>
                        <option value="autocomplete" {{ $source == 'autocomplete' ? 'selected' : '' }}>Autocomplete</option>
                        <option value="manual" {{ $source == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="intent" class="mr-2">Intención:</label>
                    <select name="intent" id="intent" class="form-control">
                        <option value="">Todas</option>
                        <option value="informational" {{ $intent == 'informational' ? 'selected' : '' }}>Informativa</option>
                        <option value="commercial" {{ $intent == 'commercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="transactional" {{ $intent == 'transactional' ? 'selected' : '' }}>Transaccional</option>
                        <option value="navigational" {{ $intent == 'navigational' ? 'selected' : '' }}>Navegacional</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <div class="form-check">
                        <input type="checkbox" name="untracked_only" id="untracked_only" class="form-check-input" value="1" {{ $untrackedOnly ? 'checked' : '' }}>
                        <label class="form-check-label" for="untracked_only">Solo no trackeadas</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Keywords -->
    <div class="card">
        <div class="card-body table-responsive">
            <table id="table-keyword-research" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Sitio</th>
                        <th>Fuente</th>
                        <th>Cluster/Tema</th>
                        <th>Intención</th>
                        <th>Posición</th>
                        <th>Clics</th>
                        <th>Impresiones</th>
                        <th>Volumen</th>
                        <th>Dificultad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keywords as $keyword)
                        <tr>
                            <td><strong>{{ $keyword->keyword }}</strong></td>
                            <td>
                                <a href="{{ route('sites.show', $keyword->site) }}">
                                    {{ $keyword->site->nombre }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ ucfirst($keyword->source) }}</span>
                            </td>
                            <td>
                                @if($keyword->cluster)
                                    <span class="badge badge-info">{{ $keyword->cluster }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{!! $keyword->intent_badge !!}</td>
                            <td>
                                @if($keyword->current_position)
                                    <span class="badge badge-info">{{ $keyword->current_position }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $keyword->clicks ?? '-' }}</td>
                            <td>{{ $keyword->impressions ?? '-' }}</td>
                            <td>
                                @if($keyword->search_volume)
                                    {{ number_format($keyword->search_volume) }}
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" onclick="estimateVolume({{ $keyword->id }})">
                                        Estimar
                                    </button>
                                @endif
                            </td>
                            <td>{!! $keyword->difficulty_badge !!}</td>
                            <td>
                                @if($keyword->is_tracked)
                                    <span class="badge badge-success">Trackeada</span>
                                @else
                                    <span class="badge badge-warning">No trackeada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(!$keyword->is_tracked)
                                        <form action="{{ route('keyword-research.add-to-tracking', $keyword) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Agregar al tracking">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal{{ $keyword->id }}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('keyword-research.destroy', $keyword) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta keyword?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal de Edición -->
                                <div class="modal fade" id="editModal{{ $keyword->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('keyword-research.update', $keyword) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Keyword: {{ $keyword->keyword }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Volumen de Búsqueda</label>
                                                        <input type="number" name="search_volume" class="form-control" value="{{ $keyword->search_volume }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Dificultad (0-100)</label>
                                                        <input type="number" name="difficulty" class="form-control" min="0" max="100" step="0.01" value="{{ $keyword->difficulty }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>CPC</label>
                                                        <input type="number" name="cpc" class="form-control" step="0.01" value="{{ $keyword->cpc }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Intención</label>
                                                        <select name="intent" class="form-control">
                                                            <option value="informational" {{ $keyword->intent == 'informational' ? 'selected' : '' }}>Informativa</option>
                                                            <option value="commercial" {{ $keyword->intent == 'commercial' ? 'selected' : '' }}>Comercial</option>
                                                            <option value="transactional" {{ $keyword->intent == 'transactional' ? 'selected' : '' }}>Transaccional</option>
                                                            <option value="navigational" {{ $keyword->intent == 'navigational' ? 'selected' : '' }}>Navegacional</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Notas</label>
                                                        <textarea name="notes" class="form-control" rows="3">{{ $keyword->notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if($keywords->isEmpty())
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                No hay keywords en investigación. Usa las herramientas de búsqueda arriba para encontrar keywords.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#table-keyword-research').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [11] } // Columna de acciones no ordenable
                ]
            });

            // Función para estimar volumen
            $('.btn-outline-secondary').click(function() {
                alert('Funcionalidad de estimación de volumen próximamente disponible');
            });
        });
    </script>
@stop

