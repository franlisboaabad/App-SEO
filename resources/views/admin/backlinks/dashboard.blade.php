@extends('adminlte::page')

@section('title', 'Dashboard Backlinks - ' . $site->nombre)

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-link"></i> Dashboard Backlinks: {{ $site->nombre }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('backlinks.create', ['site_id' => $site->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Backlink
            </a>
            <a href="{{ route('backlinks.index', ['site_id' => $site->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas -->
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

    <div class="row">
        <div class="col-md-8">
            <!-- Top Dominios -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Top 10 Dominios que Enlazan</h3>
                </div>
                <div class="card-body">
                    @if($topDomains->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Dominio</th>
                                        <th>Cantidad de Enlaces</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topDomains as $domain)
                                        <tr>
                                            <td><strong>{{ $domain->source_domain }}</strong></td>
                                            <td><span class="badge badge-info">{{ $domain->count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay datos de dominios.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Distribución por Tipo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribución</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Por Tipo:</strong>
                        <ul class="list-unstyled mt-2">
                            <li><span class="badge badge-success">Dofollow:</span> {{ $stats['dofollow'] }}</li>
                            <li><span class="badge badge-secondary">Nofollow:</span> {{ $stats['nofollow'] }}</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <strong>Por Fuente:</strong>
                        <ul class="list-unstyled mt-2">
                            <li><span class="badge badge-primary">GSC:</span> {{ $stats['from_gsc'] }}</li>
                            <li><span class="badge badge-info">Manual:</span> {{ $stats['manual'] }}</li>
                        </ul>
                    </div>
                    @if($stats['toxic'] > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ $stats['toxic'] }}</strong> backlink(s) marcado(s) como tóxico(s)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Backlinks Recientes -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clock"></i> Backlinks Recientes</h3>
        </div>
        <div class="card-body">
            @if($backlinks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dominio</th>
                                <th>URL Fuente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
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
                                    <td>{!! $backlink->link_type_badge !!}</td>
                                    <td>
                                        @if($backlink->is_toxic)
                                            <span class="badge badge-danger">Tóxico</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
                                        @endif
                                    </td>
                                    <td>{{ $backlink->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('backlinks.show', $backlink->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay backlinks recientes.
                </div>
            @endif
        </div>
    </div>
@stop

