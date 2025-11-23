@extends('adminlte::page')

@section('title', 'Dashboard SEO')

@section('content_header')
    <h1>Dashboard SEO</h1>
@stop

@section('content')
    <!-- Estadísticas principales -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSites }}</h3>
                    <p>Sitios Web</p>
                </div>
                <div class="icon">
                    <i class="fas fa-globe"></i>
                </div>
                <a href="{{ route('sites.index') }}" class="small-box-footer">
                    Ver todos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $activeSites }}</h3>
                    <p>Sitios Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('sites.index') }}" class="small-box-footer">
                    Ver sitios <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($totalMetrics) }}</h3>
                    <p>Métricas Totales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Últimos 7 días <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalAudits }}</h3>
                    <p>Auditorías Totales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-search"></i>
                </div>
                <a href="#" class="small-box-footer">
                    {{ $completedAudits }} completadas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Métricas de los últimos 7 días -->
    @if($metricsLast7Days && ($metricsLast7Days->total_clicks > 0 || $metricsLast7Days->total_impressions > 0))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Métricas de los Últimos 7 Días
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-mouse-pointer"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Clics</span>
                                        <span class="info-box-number">{{ number_format($metricsLast7Days->total_clicks ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-eye"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Impresiones</span>
                                        <span class="info-box-number">{{ number_format($metricsLast7Days->total_impressions ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">CTR Promedio</span>
                                        <span class="info-box-number">{{ number_format(($metricsLast7Days->avg_ctr ?? 0) * 100, 2) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Posición Promedio</span>
                                        <span class="info-box-number">{{ number_format($metricsLast7Days->avg_position ?? 0, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Últimas Auditorías -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Últimas Auditorías
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($recentAudits->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sitio</th>
                                    <th>Estado</th>
                                    <th>Score</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAudits as $audit)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sites.show', $audit->site) }}">
                                                {{ Str::limit($audit->site->nombre, 20) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($audit->status == 'completed')
                                                <span class="badge badge-success">Completada</span>
                                            @elseif($audit->status == 'processing')
                                                <span class="badge badge-info">Procesando</span>
                                            @elseif($audit->status == 'pending')
                                                <span class="badge badge-warning">Pendiente</span>
                                            @else
                                                <span class="badge badge-danger">Fallida</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($audit->result)
                                                @php
                                                    $score = $audit->result->seo_score;
                                                    $badgeClass = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge badge-{{ $badgeClass }}">{{ $score }}/100</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted p-3">No hay auditorías recientes</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sitios Recientes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i> Sitios Recientes
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($recentSites->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sitio</th>
                                    <th>Dominio</th>
                                    <th>Estado</th>
                                    <th>Métricas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSites as $site)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sites.show', $site) }}">
                                                {{ $site->nombre }}
                                            </a>
                                        </td>
                                        <td>{{ $site->dominio_base }}</td>
                                        <td>
                                            @if($site->estado)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $site->seoMetrics()->count() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted p-3">No hay sitios registrados</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Auditorías -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Estado de Auditorías
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completadas</span>
                                    <span class="info-box-number">{{ $auditsByStatus['completed'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pendientes</span>
                                    <span class="info-box-number">{{ $auditsByStatus['pending'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-spinner"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Procesando</span>
                                    <span class="info-box-number">{{ $auditsByStatus['processing'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fallidas</span>
                                    <span class="info-box-number">{{ $auditsByStatus['failed'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sitios con más métricas -->
    @if($topSitesByMetrics->count() > 0)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-trophy"></i> Sitios con Más Métricas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sitio</th>
                                        <th>Dominio</th>
                                        <th>Total Métricas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSitesByMetrics as $index => $site)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $site->nombre }}</strong>
                                            </td>
                                            <td>{{ $site->dominio_base }}</td>
                                            <td>
                                                <span class="badge badge-primary">{{ number_format($site->seo_metrics_count) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('sites.dashboard', $site) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-line"></i> Dashboard
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Dashboard SEO cargado');
    </script>
@stop
