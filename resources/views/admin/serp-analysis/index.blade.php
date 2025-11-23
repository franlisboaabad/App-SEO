@extends('adminlte::page')

@section('title', 'Análisis de SERP')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-search"></i> Análisis de SERP</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('serp-analysis.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Análisis
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

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('serp-analysis.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label>Sitio:</label>
                        <select name="site_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los sitios</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                    {{ $site->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Keyword:</label>
                        <input type="text" name="keyword" class="form-control" value="{{ $keyword }}" placeholder="Buscar keyword...">
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Análisis -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Análisis de SERP ({{ $analyses->total() }})</h3>
        </div>
        <div class="card-body">
            @if($analyses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Sitio</th>
                                <th>Posición</th>
                                <th>Título</th>
                                <th>URL</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyses as $analysis)
                                <tr>
                                    <td><strong>{{ $analysis->getAttribute('keyword') ?: 'N/A' }}</strong></td>
                                    <td>
                                        @if($analysis->site)
                                            <a href="{{ route('sites.show', $analysis->site->id) }}">
                                                {{ $analysis->site->nombre }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{!! $analysis->position_badge !!}</td>
                                    <td>
                                        @if($analysis->title)
                                            @php
                                                $title = $analysis->title;
                                                echo mb_strlen($title) > 50 ? mb_substr($title, 0, 50) . '...' : $title;
                                            @endphp
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($analysis->url)
                                            <a href="{{ $analysis->url }}" target="_blank" rel="noopener">
                                                @php
                                                    $url = $analysis->display_url ?? $analysis->url;
                                                    echo mb_strlen($url) > 40 ? mb_substr($url, 0, 40) . '...' : $url;
                                                @endphp
                                                <i class="fas fa-external-link-alt ml-1"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No rankea</span>
                                        @endif
                                    </td>
                                    <td>{{ $analysis->analysis_date_formatted }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('serp-analysis.show', $analysis) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('serp-analysis.reanalyze', $analysis) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Re-analizar">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('serp-analysis.destroy', $analysis) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este análisis?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $analyses->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay análisis de SERP.
                    <a href="{{ route('serp-analysis.create') }}">Crea uno nuevo</a> para comenzar.
                </div>
            @endif
        </div>
    </div>
@stop

