@extends('adminlte::page')

@section('title', 'Historial de Auditorías - ' . $site->nombre)

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Historial de Auditorías: {{ $site->nombre }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('sites.show', $site) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Sitio
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Auditorías</h3>
        </div>
        <div class="card-body">
            @if($audits->count() > 0)
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>URL</th>
                            <th>Estado</th>
                            <th>Score SEO</th>
                            <th>Errores</th>
                            <th>Advertencias</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                            <tr>
                                <td>{{ $audit->id }}</td>
                                <td>
                                    <a href="{{ $audit->url }}" target="_blank" title="{{ $audit->url }}">
                                        {{ Str::limit($audit->url, 60) }}
                                        <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                </td>
                                <td>
                                    @if($audit->status == 'completed')
                                        <span class="badge badge-success">Completada</span>
                                    @elseif($audit->status == 'processing')
                                        <span class="badge badge-info">Procesando</span>
                                    @elseif($audit->status == 'pending')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($audit->status == 'failed')
                                        <span class="badge badge-danger">Fallida</span>
                                    @endif
                                </td>
                                <td>
                                    @if($audit->result)
                                        @php
                                            $score = $audit->result->seo_score;
                                            $badgeClass = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $score }}/100
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($audit->result && count($audit->result->errors ?? []) > 0)
                                        <span class="badge badge-danger">{{ count($audit->result->errors) }}</span>
                                    @else
                                        <span class="badge badge-success">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($audit->result && count($audit->result->warnings ?? []) > 0)
                                        <span class="badge badge-warning">{{ count($audit->result->warnings) }}</span>
                                    @else
                                        <span class="badge badge-secondary">0</span>
                                    @endif
                                </td>
                                <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if($audit->status == 'completed' && $audit->result)
                                        <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @elseif($audit->status == 'failed')
                                        <span class="text-danger" title="{{ $audit->error_message }}">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $audits->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay auditorías registradas para este sitio.
                    <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-primary ml-2">
                        <i class="fas fa-search"></i> Ejecutar Primera Auditoría
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    @if($audits->count() > 0)
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Completadas</span>
                        <span class="info-box-number">{{ $audits->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pendientes</span>
                        <span class="info-box-number">{{ $audits->where('status', 'pending')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Fallidas</span>
                        <span class="info-box-number">{{ $audits->where('status', 'failed')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Score Promedio</span>
                        <span class="info-box-number">
                            @php
                                $completedAudits = $audits->where('status', 'completed')->filter(function($a) { return $a->result; });
                                $avgScore = $completedAudits->avg(function($a) { return $a->result->seo_score; });
                            @endphp
                            {{ $avgScore ? number_format($avgScore, 1) : 'N/A' }}
                        </span>
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
        console.log('Historial de auditorías para sitio: {{ $site->nombre }}');
    </script>
@stop

