@extends('adminlte::page')

@section('title', 'Clusters de Keywords')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-sitemap"></i> Clusters de Keywords</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('keyword-research.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Investigación
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
            <form method="GET" action="{{ route('keyword-research.clusters') }}" class="form-inline">
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <!-- Clusters -->
    @forelse($clusters as $clusterName => $clusterData)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-folder"></i> {{ $clusterName }}
                    <span class="badge badge-primary ml-2">{{ $clusterData['count'] }} keywords</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Total Keywords:</strong> {{ $clusterData['count'] }}
                    </div>
                    <div class="col-md-3">
                        <strong>Volumen Promedio:</strong>
                        @if($clusterData['avg_volume'])
                            {{ number_format($clusterData['avg_volume']) }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Intenciones:</strong>
                        @foreach($clusterData['intents'] as $intent)
                            @php
                                $badges = [
                                    'informational' => 'info',
                                    'commercial' => 'warning',
                                    'transactional' => 'success',
                                    'navigational' => 'primary',
                                ];
                                $badgeClass = $badges[$intent] ?? 'secondary';
                                $labels = [
                                    'informational' => 'Informativa',
                                    'commercial' => 'Comercial',
                                    'transactional' => 'Transaccional',
                                    'navigational' => 'Navegacional',
                                ];
                                $label = $labels[$intent] ?? ucfirst($intent);
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Intención</th>
                                <th>Posición</th>
                                <th>Volumen</th>
                                <th>Dificultad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clusterData['keywords'] as $keyword)
                                <tr>
                                    <td><strong>{{ $keyword->keyword }}</strong></td>
                                    <td>{!! $keyword->intent_badge !!}</td>
                                    <td>
                                        @if($keyword->current_position)
                                            <span class="badge badge-info">{{ $keyword->current_position }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $keyword->search_volume ? number_format($keyword->search_volume) : '-' }}</td>
                                    <td>{!! $keyword->difficulty_badge !!}</td>
                                    <td>
                                        @if($keyword->is_tracked)
                                            <span class="badge badge-success">Trackeada</span>
                                        @else
                                            <span class="badge badge-warning">No trackeada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$keyword->is_tracked)
                                            <form action="{{ route('keyword-research.add-to-tracking', $keyword) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Agregar al tracking">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay clusters disponibles.
            <a href="{{ route('keyword-research.index', ['site_id' => $siteId]) }}">Busca keywords primero</a> y luego asigna clusters.
        </div>
    @endforelse
@stop

