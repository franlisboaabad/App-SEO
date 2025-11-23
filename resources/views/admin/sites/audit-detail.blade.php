@extends('adminlte::page')

@section('title', 'Detalles de Auditoría')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Detalles de Auditoría SEO</h1>
        </div>
        <div class="col-md text-right">
            @if($audit->result)
                <a href="{{ route('audits.report', $audit) }}" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            @endif
            <a href="{{ route('sites.audits', $audit->site) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Historial
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(!$audit->result)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Esta auditoría no tiene resultados disponibles.
            @if($audit->status == 'failed')
                <strong>Error:</strong> {{ $audit->error_message }}
            @endif
        </div>
    @else
        @php
            $result = $audit->result;
            $score = $result->seo_score;
            $scoreClass = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
        @endphp

        <!-- Score SEO -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-{{ $scoreClass }}">
                        <h3 class="card-title text-white">
                            <i class="fas fa-chart-pie"></i> Score SEO: {{ $score }}/100
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-2" style="height: 30px;">
                            <div class="progress-bar bg-{{ $scoreClass }} progress-bar-striped"
                                 role="progressbar"
                                 style="width: {{ $score }}%"
                                 aria-valuenow="{{ $score }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                {{ $score }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($score >= 70)
                                <i class="fas fa-check-circle text-success"></i> Excelente - Tu página tiene un buen SEO
                            @elseif($score >= 50)
                                <i class="fas fa-exclamation-circle text-warning"></i> Regular - Hay aspectos que mejorar
                            @else
                                <i class="fas fa-times-circle text-danger"></i> Necesita Mejoras - Hay varios problemas SEO
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información General -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">URL Auditada</th>
                                <td>
                                    <a href="{{ $audit->url }}" target="_blank">
                                        {{ $audit->url }}
                                        <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    @if($audit->status == 'completed')
                                        <span class="badge badge-success">Completada</span>
                                    @else
                                        <span class="badge badge-{{ $audit->status == 'failed' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($audit->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha de Auditoría</th>
                                <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Status Code HTTP</th>
                                <td>
                                    @if($result->status_code)
                                        <span class="badge badge-{{ $result->status_code >= 400 ? 'danger' : 'success' }}">
                                            {{ $result->status_code }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>TTFB (Time to First Byte)</th>
                                <td>
                                    @if($result->ttfb)
                                        {{ number_format($result->ttfb, 3) }}s
                                        @if($result->ttfb > 0.6)
                                            <span class="badge badge-warning ml-2">Lento</span>
                                        @else
                                            <span class="badge badge-success ml-2">Rápido</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SEO On-Page -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-search"></i> SEO On-Page</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Title</th>
                                <td>
                                    @if($result->title)
                                        <strong>{{ $result->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ strlen($result->title) }} caracteres</small>
                                    @else
                                        <span class="badge badge-danger">No encontrado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Meta Description</th>
                                <td>
                                    @if($result->meta_description)
                                        {{ $result->meta_description }}
                                        <br>
                                        <small class="text-muted">{{ strlen($result->meta_description) }} caracteres</small>
                                    @else
                                        <span class="badge badge-warning">No encontrada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>H1</th>
                                <td>
                                    <span class="badge badge-{{ $result->h1_count == 1 ? 'success' : ($result->h1_count == 0 ? 'danger' : 'warning') }}">
                                        {{ $result->h1_count }} encontrado(s)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>H2</th>
                                <td><span class="badge badge-info">{{ $result->h2_count }}</span></td>
                            </tr>
                            <tr>
                                <th>H3</th>
                                <td><span class="badge badge-info">{{ $result->h3_count }}</span></td>
                            </tr>
                            <tr>
                                <th>Imágenes sin ALT</th>
                                <td>
                                    @if($result->images_total > 0)
                                        <span class="badge badge-{{ $result->images_without_alt > 0 ? 'warning' : 'success' }}">
                                            {{ $result->images_without_alt }} / {{ $result->images_total }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">0</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Canonical</th>
                                <td>
                                    @if($result->canonical)
                                        <a href="{{ $result->canonical }}" target="_blank">{{ $result->canonical }}</a>
                                    @else
                                        <span class="badge badge-warning">No encontrado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Robots Meta</th>
                                <td>
                                    @if($result->robots_meta)
                                        <span class="badge badge-info">{{ $result->robots_meta }}</span>
                                    @else
                                        <span class="badge badge-secondary">No especificado</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-link"></i> Análisis de Links</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-link"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Links Internos</span>
                                        <span class="info-box-number">{{ $result->internal_links_count }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-external-link-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Links Externos</span>
                                        <span class="info-box-number">{{ $result->external_links_count }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-unlink"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Links Rotos</span>
                                        <span class="info-box-number">{{ $result->broken_links_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Errores y Advertencias -->
        @if(count($result->errors ?? []) > 0 || count($result->warnings ?? []) > 0)
            <div class="row mt-3">
                @if(count($result->errors ?? []) > 0)
                    <div class="col-md-6">
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Errores ({{ count($result->errors) }})</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @foreach($result->errors as $error)
                                        <li class="mb-2">
                                            <i class="fas fa-times-circle text-danger"></i> {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if(count($result->warnings ?? []) > 0)
                    <div class="col-md-6">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Advertencias ({{ count($result->warnings) }})</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @foreach($result->warnings as $warning)
                                        <li class="mb-2">
                                            <i class="fas fa-exclamation-triangle text-warning"></i> {{ $warning }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ¡Excelente! No se encontraron errores ni advertencias en esta auditoría.
                    </div>
                </div>
            </div>
        @endif
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Detalles de auditoría: {{ $audit->id }}');
    </script>
@stop

