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
                            <tr>
                                <th>Palabras en Contenido</th>
                                <td>
                                    @if($result->word_count)
                                        <span class="badge badge-{{ $result->word_count >= 500 ? 'success' : ($result->word_count >= 300 ? 'warning' : 'danger') }}">
                                            {{ number_format($result->word_count) }} palabras
                                        </span>
                                        @if($result->word_count < 300)
                                            <small class="text-danger ml-2">(Recomendado: 300+)</small>
                                        @elseif($result->word_count < 500)
                                            <small class="text-warning ml-2">(Recomendado: 500+)</small>
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
        </div>

        <!-- Análisis de Contenido -->
        @if($result->word_count || !empty($result->keyword_density))
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Análisis de Contenido</h3>
                        </div>
                        <div class="card-body">
                            @if($result->word_count)
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-font"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total de Palabras</span>
                                                <span class="info-box-number">{{ number_format($result->word_count) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($result->keyword_density))
                                <h4>Densidad de Keywords (Top 10)</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Keyword</th>
                                                <th>Frecuencia</th>
                                                <th>Densidad</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($result->keyword_density as $keyword)
                                                @php
                                                    $densityStatus = $keyword['density'] >= 1 && $keyword['density'] <= 2 ? 'success' : ($keyword['density'] < 1 ? 'warning' : 'danger');
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $keyword['keyword'] }}</strong></td>
                                                    <td>{{ $keyword['frequency'] }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $densityStatus }}">
                                                            {{ $keyword['density'] }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($keyword['density'] >= 1 && $keyword['density'] <= 2)
                                                            <span class="badge badge-success">Óptimo</span>
                                                        @elseif($keyword['density'] < 1)
                                                            <span class="badge badge-warning">Bajo</span>
                                                        @else
                                                            <span class="badge badge-danger">Alto (keyword stuffing?)</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if(!empty($result->content_suggestions))
                                <h4 class="mt-3">Sugerencias de Contenido</h4>
                                @foreach($result->content_suggestions as $suggestion)
                                    <div class="alert alert-{{ $suggestion['type'] == 'warning' ? 'warning' : 'info' }}">
                                        <i class="fas fa-{{ $suggestion['type'] == 'warning' ? 'exclamation-triangle' : 'info-circle' }}"></i>
                                        {{ $suggestion['message'] }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

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

        <!-- Tabs para Links -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="internal-links-tab" data-toggle="tab" href="#internal-links" role="tab">
                                    <i class="fas fa-link"></i> Links Internos ({{ $result->internal_links_count }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="external-links-tab" data-toggle="tab" href="#external-links" role="tab">
                                    <i class="fas fa-external-link-alt"></i> Links Externos ({{ $result->external_links_count }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="broken-links-tab" data-toggle="tab" href="#broken-links" role="tab">
                                    <i class="fas fa-unlink"></i> Links Rotos ({{ $result->broken_links_count }})
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Links Internos -->
                            <div class="tab-pane fade show active" id="internal-links" role="tabpanel">
                                <div class="mb-3">
                                    <a href="{{ route('audits.export-internal-links', $audit) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Exportar a Excel
                                    </a>
                                </div>
                                <table class="table table-striped table-hover" id="table-internal-links">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>URL</th>
                                            <th>Texto del Link</th>
                                            <th>Href Original</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($result->internal_links ?? [] as $index => $link)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener">
                                                        {{ Str::limit($link['url'] ?? '', 60) }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $link['text'] ?? '(sin texto)' }}</td>
                                                <td><code>{{ Str::limit($link['href'] ?? '', 50) }}</code></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay links internos</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tab Links Externos -->
                            <div class="tab-pane fade" id="external-links" role="tabpanel">
                                <div class="mb-3">
                                    <a href="{{ route('audits.export-external-links', $audit) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Exportar a Excel
                                    </a>
                                </div>
                                <table class="table table-striped table-hover" id="table-external-links">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>URL</th>
                                            <th>Texto del Link</th>
                                            <th>Href Original</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($result->external_links ?? [] as $index => $link)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener">
                                                        {{ Str::limit($link['url'] ?? '', 60) }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $link['text'] ?? '(sin texto)' }}</td>
                                                <td><code>{{ Str::limit($link['href'] ?? '', 50) }}</code></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay links externos</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tab Links Rotos -->
                            <div class="tab-pane fade" id="broken-links" role="tabpanel">
                                <div class="mb-3">
                                    <a href="{{ route('audits.export-broken-links', $audit) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Exportar a Excel
                                    </a>
                                </div>
                                <table class="table table-striped table-hover" id="table-broken-links">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>URL</th>
                                            <th>Texto del Link</th>
                                            <th>Href Original</th>
                                            <th>Status Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($result->broken_links ?? [] as $index => $link)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener">
                                                        {{ Str::limit($link['url'] ?? '', 60) }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                </td>
                                                <td>{{ $link['text'] ?? '(sin texto)' }}</td>
                                                <td><code>{{ Str::limit($link['href'] ?? '', 50) }}</code></td>
                                                <td>
                                                    @if(isset($link['status_code']) && $link['status_code'] > 0)
                                                        <span class="badge badge-danger">{{ $link['status_code'] }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">Error</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No hay links rotos</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis de Velocidad (PageSpeed Insights) -->
        @if($result->pagespeed_score_mobile || $result->pagespeed_score_desktop)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title text-white">
                                <i class="fas fa-tachometer-alt"></i> Análisis de Velocidad (PageSpeed Insights)
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Scores Generales -->
                            <div class="row mb-4">
                                @if($result->pagespeed_score_mobile)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-primary">
                                                <h4 class="card-title text-white text-center">
                                                    <i class="fas fa-mobile-alt"></i> Mobile
                                                </h4>
                                            </div>
                                            <div class="card-body text-center">
                                                @php
                                                    $mobileScore = $result->pagespeed_score_mobile;
                                                    $mobileClass = $mobileScore >= 90 ? 'success' : ($mobileScore >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <h2 class="text-{{ $mobileClass }}">
                                                    {{ $mobileScore }}/100
                                                </h2>
                                                <div class="progress mb-2" style="height: 25px;">
                                                    <div class="progress-bar bg-{{ $mobileClass }} progress-bar-striped"
                                                         role="progressbar"
                                                         style="width: {{ $mobileScore }}%"
                                                         aria-valuenow="{{ $mobileScore }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $mobileScore }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($result->pagespeed_score_desktop)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-secondary">
                                                <h4 class="card-title text-white text-center">
                                                    <i class="fas fa-desktop"></i> Desktop
                                                </h4>
                                            </div>
                                            <div class="card-body text-center">
                                                @php
                                                    $desktopScore = $result->pagespeed_score_desktop;
                                                    $desktopClass = $desktopScore >= 90 ? 'success' : ($desktopScore >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <h2 class="text-{{ $desktopClass }}">
                                                    {{ $desktopScore }}/100
                                                </h2>
                                                <div class="progress mb-2" style="height: 25px;">
                                                    <div class="progress-bar bg-{{ $desktopClass }} progress-bar-striped"
                                                         role="progressbar"
                                                         style="width: {{ $desktopScore }}%"
                                                         aria-valuenow="{{ $desktopScore }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $desktopScore }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Core Web Vitals -->
                            <h4 class="mb-3"><i class="fas fa-chart-line"></i> Core Web Vitals</h4>
                            <div class="row">
                                <!-- Mobile Core Web Vitals -->
                                @if($result->fcp_mobile || $result->lcp_mobile || $result->cls_mobile || $result->fid_mobile || $result->tti_mobile)
                                    <div class="col-md-6">
                                        <h5 class="text-primary"><i class="fas fa-mobile-alt"></i> Mobile</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Métrica</th>
                                                        <th>Valor</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($result->fcp_mobile)
                                                        <tr>
                                                            <td><strong>FCP</strong><br><small>First Contentful Paint</small></td>
                                                            <td>{{ number_format($result->fcp_mobile, 2) }}s</td>
                                                            <td>
                                                                @if($result->fcp_mobile <= 1.8)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->fcp_mobile <= 3.0)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->lcp_mobile)
                                                        <tr>
                                                            <td><strong>LCP</strong><br><small>Largest Contentful Paint</small></td>
                                                            <td>{{ number_format($result->lcp_mobile, 2) }}s</td>
                                                            <td>
                                                                @if($result->lcp_mobile <= 2.5)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->lcp_mobile <= 4.0)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->cls_mobile !== null)
                                                        <tr>
                                                            <td><strong>CLS</strong><br><small>Cumulative Layout Shift</small></td>
                                                            <td>{{ number_format($result->cls_mobile, 4) }}</td>
                                                            <td>
                                                                @if($result->cls_mobile <= 0.1)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->cls_mobile <= 0.25)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->fid_mobile)
                                                        <tr>
                                                            <td><strong>FID</strong><br><small>First Input Delay</small></td>
                                                            <td>{{ number_format($result->fid_mobile, 2) }}s</td>
                                                            <td>
                                                                @if($result->fid_mobile <= 0.1)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->fid_mobile <= 0.3)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->tti_mobile)
                                                        <tr>
                                                            <td><strong>TTI</strong><br><small>Time to Interactive</small></td>
                                                            <td>{{ number_format($result->tti_mobile, 2) }}s</td>
                                                            <td>
                                                                @if($result->tti_mobile <= 3.8)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->tti_mobile <= 7.3)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Desktop Core Web Vitals -->
                                @if($result->fcp_desktop || $result->lcp_desktop || $result->cls_desktop || $result->fid_desktop || $result->tti_desktop)
                                    <div class="col-md-6">
                                        <h5 class="text-secondary"><i class="fas fa-desktop"></i> Desktop</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Métrica</th>
                                                        <th>Valor</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($result->fcp_desktop)
                                                        <tr>
                                                            <td><strong>FCP</strong><br><small>First Contentful Paint</small></td>
                                                            <td>{{ number_format($result->fcp_desktop, 2) }}s</td>
                                                            <td>
                                                                @if($result->fcp_desktop <= 1.8)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->fcp_desktop <= 3.0)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->lcp_desktop)
                                                        <tr>
                                                            <td><strong>LCP</strong><br><small>Largest Contentful Paint</small></td>
                                                            <td>{{ number_format($result->lcp_desktop, 2) }}s</td>
                                                            <td>
                                                                @if($result->lcp_desktop <= 2.5)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->lcp_desktop <= 4.0)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->cls_desktop !== null)
                                                        <tr>
                                                            <td><strong>CLS</strong><br><small>Cumulative Layout Shift</small></td>
                                                            <td>{{ number_format($result->cls_desktop, 4) }}</td>
                                                            <td>
                                                                @if($result->cls_desktop <= 0.1)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->cls_desktop <= 0.25)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->fid_desktop)
                                                        <tr>
                                                            <td><strong>FID</strong><br><small>First Input Delay</small></td>
                                                            <td>{{ number_format($result->fid_desktop, 2) }}s</td>
                                                            <td>
                                                                @if($result->fid_desktop <= 0.1)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->fid_desktop <= 0.3)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($result->tti_desktop)
                                                        <tr>
                                                            <td><strong>TTI</strong><br><small>Time to Interactive</small></td>
                                                            <td>{{ number_format($result->tti_desktop, 2) }}s</td>
                                                            <td>
                                                                @if($result->tti_desktop <= 3.8)
                                                                    <span class="badge badge-success">Bueno</span>
                                                                @elseif($result->tti_desktop <= 7.3)
                                                                    <span class="badge badge-warning">Mejorable</span>
                                                                @else
                                                                    <span class="badge badge-danger">Lento</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Recomendaciones de Optimización -->
                            @if(!empty($result->pagespeed_recommendations))
                                <div class="mt-4">
                                    <h4 class="mb-3"><i class="fas fa-lightbulb"></i> Recomendaciones de Optimización</h4>
                                    <div class="row">
                                        @foreach($result->pagespeed_recommendations as $recommendation)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-{{ $recommendation['impact'] == 'high' ? 'danger' : 'warning' }}">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <i class="fas fa-{{ $recommendation['impact'] == 'high' ? 'exclamation-triangle text-danger' : 'info-circle text-warning' }}"></i>
                                                            {{ $recommendation['title'] }}
                                                        </h5>
                                                        <p class="card-text text-muted small">{{ $recommendation['description'] ?? '' }}</p>
                                                        <span class="badge badge-{{ $recommendation['impact'] == 'high' ? 'danger' : 'warning' }}">
                                                            Score: {{ $recommendation['score'] }}/100
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // DataTable para Links Internos
            $('#table-internal-links').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [1, 2, 3] }
                ]
            });

            // DataTable para Links Externos
            $('#table-external-links').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "pageLength": 25,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [1, 2, 3] }
                ]
            });

            // DataTable para Links Rotos
            $('#table-broken-links').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "pageLength": 25,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [1, 2, 3, 4] }
                ]
            });
        });
    </script>
@stop

