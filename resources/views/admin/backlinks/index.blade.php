@extends('adminlte::page')

@section('title', 'Backlinks')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-link"></i> Backlinks</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('backlinks.create', ['site_id' => $siteId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Backlink
            </a>
            @if($siteId)
                <a href="{{ route('backlinks.dashboard', $siteId) }}" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> Dashboard
                </a>
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

    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session()->get('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Estadísticas -->
    @if($stats)
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-link"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Backlinks</span>
                        <span class="info-box-number">{{ $stats['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Dofollow</span>
                        <span class="info-box-number">{{ $stats['dofollow'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tóxicos</span>
                        <span class="info-box-number">{{ $stats['toxic'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Dominios Únicos</span>
                        <span class="info-box-number">{{ $stats['domains'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('backlinks.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sitio</label>
                            <select name="site_id" class="form-control">
                                <option value="">Todos los sitios</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                        {{ $site->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tipo de Enlace</label>
                            <select name="link_type" class="form-control">
                                <option value="">Todos</option>
                                <option value="dofollow" {{ request('link_type') == 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                                <option value="nofollow" {{ request('link_type') == 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                                <option value="sponsored" {{ request('link_type') == 'sponsored' ? 'selected' : '' }}>Sponsored</option>
                                <option value="ugc" {{ request('link_type') == 'ugc' ? 'selected' : '' }}>UGC</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fuente</label>
                            <select name="source_type" class="form-control">
                                <option value="">Todas</option>
                                <option value="gsc" {{ request('source_type') == 'gsc' ? 'selected' : '' }}>GSC</option>
                                <option value="manual" {{ request('source_type') == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tóxicos</label>
                            <select name="toxic" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ request('toxic') == '1' ? 'selected' : '' }}>Solo Tóxicos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Dominio, URL, anchor..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('backlinks.index', ['site_id' => $siteId]) }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                        @if($siteId)
                            <form action="{{ route('backlinks.sync-gsc', $siteId) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fab fa-google"></i> Sincronizar desde GSC
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Backlinks -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Backlinks ({{ $backlinks->total() }})</h3>
        </div>
        <div class="card-body">
            @if($backlinks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dominio Fuente</th>
                                <th>URL Fuente</th>
                                <th>URL Destino</th>
                                <th>Anchor Text</th>
                                <th>Tipo</th>
                                <th>Fuente</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backlinks as $backlink)
                                <tr class="{{ $backlink->is_toxic ? 'table-danger' : '' }}">
                                    <td><strong>{{ $backlink->source_domain }}</strong></td>
                                    <td>
                                        <a href="{{ $backlink->source_url }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ Str::limit($backlink->source_url, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ $backlink->target_url }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ Str::limit($backlink->target_url, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($backlink->anchor_text ?? 'N/A', 30) }}</td>
                                    <td>{!! $backlink->link_type_badge !!}</td>
                                    <td>{!! $backlink->source_type_badge !!}</td>
                                    <td>
                                        @if($backlink->is_toxic)
                                            <span class="badge badge-danger">Tóxico</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('backlinks.show', $backlink->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('backlinks.edit', $backlink->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('backlinks.destroy', $backlink->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este backlink?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $backlinks->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No se encontraron backlinks.
                </div>
            @endif
        </div>
    </div>
@stop

