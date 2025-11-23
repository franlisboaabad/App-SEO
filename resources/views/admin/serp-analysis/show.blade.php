@extends('adminlte::page')

@section('title', 'Análisis de SERP - ' . ($serpAnalysis->getAttribute('keyword') ?: 'N/A'))

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Análisis de SERP: <strong>{{ $serpAnalysis->getAttribute('keyword') ?: 'N/A' }}</strong></h1>
        </div>
        <div class="col-md text-right">
            @if(isset($serpAnalysis) && $serpAnalysis->id)
                <form action="{{ route('serp-analysis.reanalyze', $serpAnalysis->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning" id="reanalyzeButton">
                        <i class="fas fa-sync"></i> <span id="reanalyzeButtonText">Re-analizar</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('serp-analysis.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
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

    <!-- Información General -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Sitio:</th>
                                    <td>
                                        @if($serpAnalysis->site)
                                            <a href="{{ route('sites.show', $serpAnalysis->site->id) }}">
                                                {{ $serpAnalysis->site->nombre }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Keyword:</th>
                                    <td><strong>{{ $serpAnalysis->getAttribute('keyword') ?: 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Posición:</th>
                                    <td>{!! $serpAnalysis->position_badge !!}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Análisis:</th>
                                    <td>{{ $serpAnalysis->analysis_date_formatted }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($serpAnalysis->url)
                                <h5>Tu Snippet en SERP</h5>
                                <div class="border p-3 bg-light">
                                    <div class="mb-2">
                                        <strong style="color: #1a0dab; font-size: 18px;">{{ $serpAnalysis->title ?? 'Sin título' }}</strong>
                                    </div>
                                    <div class="mb-2" style="color: #006621; font-size: 14px;">
                                        {{ $serpAnalysis->display_url ?? $serpAnalysis->url }}
                                    </div>
                                    <div style="color: #545454; font-size: 14px;">
                                        {{ $serpAnalysis->description ?? 'Sin descripción' }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ $serpAnalysis->url }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Ver URL
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Tu sitio no aparece en los primeros 10 resultados para esta keyword.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competidores (Top 10) -->
    @if(!empty($serpAnalysis->competitors))
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Top 10 Resultados (Competidores)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Título</th>
                                        <th width="20%">URL</th>
                                        <th width="45%">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serpAnalysis->competitors as $competitor)
                                        <tr class="{{ $competitor['url'] == $serpAnalysis->url ? 'table-success' : '' }}">
                                            <td>
                                                <span class="badge badge-{{ $competitor['position'] <= 3 ? 'success' : ($competitor['position'] <= 10 ? 'info' : 'secondary') }}">
                                                    {{ $competitor['position'] }}
                                                </span>
                                                @if($competitor['url'] == $serpAnalysis->url)
                                                    <i class="fas fa-check-circle text-success ml-1" title="Tu sitio"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $competitor['title'] ?? 'Sin título' }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ $competitor['url'] ?? '#' }}" target="_blank" rel="noopener">
                                                    @php
                                                        $url = $competitor['display_url'] ?? $competitor['url'] ?? '';
                                                        echo mb_strlen($url) > 40 ? mb_substr($url, 0, 40) . '...' : $url;
                                                    @endphp
                                                    <i class="fas fa-external-link-alt ml-1"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $competitor['description'] ?? 'Sin descripción' }}</small>
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

    <!-- Sugerencias -->
    @if(!empty($serpAnalysis->suggestions))
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-lightbulb"></i> Sugerencias de Mejora</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            @foreach(explode("\n", $serpAnalysis->suggestions) as $suggestion)
                                @if(!empty(trim($suggestion)))
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-warning"></i> {{ $suggestion }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Mostrar indicador de carga al re-analizar
        const reanalyzeForm = document.querySelector('form[action*="reanalyze"]');
        if (reanalyzeForm) {
            reanalyzeForm.addEventListener('submit', function(e) {
                const button = document.getElementById('reanalyzeButton');
                const buttonText = document.getElementById('reanalyzeButtonText');

                if (button && buttonText) {
                    button.disabled = true;
                    buttonText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Re-analizando...';
                    button.classList.add('disabled');
                }
            });
        }
    </script>
@stop

