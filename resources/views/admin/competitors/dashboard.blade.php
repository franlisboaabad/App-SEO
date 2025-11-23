@extends('adminlte::page')

@section('title', 'Dashboard de Competencia - ' . $site->nombre)

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Dashboard de Competencia</h1>
            <p class="text-muted">{{ $site->nombre }} vs {{ $selectedCompetitor->nombre }}</p>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('sites.show', $site) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Selector de Competidor -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('competitors.dashboard', $site) }}">
                <div class="row">
                    <div class="col-md-4">
                        <label>Seleccionar Competidor:</label>
                        <select name="competitor_id" class="form-control" onchange="this.form.submit()">
                            @foreach($competitors as $comp)
                                <option value="{{ $comp->id }}" {{ $selectedCompetitor->id == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->nombre }} ({{ $comp->dominio_base }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de Comparación -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-key"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Keywords</span>
                    <span class="info-box-number">{{ count($comparisons) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Nosotros Mejor</span>
                    <span class="info-box-number">
                        {{ count(array_filter($comparisons, fn($c) => $c['gap'] !== null && $c['gap'] < 0)) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Competidor Mejor</span>
                    <span class="info-box-number">
                        {{ count(array_filter($comparisons, fn($c) => $c['gap'] !== null && $c['gap'] > 0)) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gaps Identificados</span>
                    <span class="info-box-number">{{ count($gaps) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gaps (Keywords donde competidor está mejor) -->
    @if(count($gaps) > 0)
        <div class="card mb-3">
            <div class="card-header bg-danger">
                <h3 class="card-title text-white">
                    <i class="fas fa-exclamation-triangle"></i> Gaps Identificados ({{ count($gaps) }})
                </h3>
                <div class="card-tools">
                    <span class="badge badge-light">Keywords donde el competidor está mejor posicionado</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Nuestra Posición</th>
                            <th>Posición Competidor</th>
                            <th>Gap</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($gaps, 0, 10) as $gap)
                            <tr>
                                <td>
                                    <strong>{{ $gap['keyword']->keyword }}</strong>
                                </td>
                                <td>
                                    @if($gap['our_position'])
                                        <span class="badge badge-info">{{ $gap['our_position'] }}</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($gap['competitor_position'])
                                        <span class="badge badge-success">{{ $gap['competitor_position'] }}</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-danger badge-lg">
                                        -{{ $gap['gap'] }} posiciones
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('keywords.show', $gap['keyword']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver Keyword
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Formulario para actualizar posiciones -->
    <div class="card mb-3">
        <div class="card-header bg-primary">
            <h3 class="card-title text-white">
                <i class="fas fa-edit"></i> Ingresar/Actualizar Posiciones del Competidor
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('competitors.update-positions', $selectedCompetitor) }}" method="POST" id="updatePositionsForm">
                @csrf
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Ingresa manualmente las posiciones del competidor para cada keyword.
                    Puedes usar herramientas como Ahrefs, SEMrush, o búsquedas manuales en Google.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Nuestra Posición</th>
                                <th>Posición Competidor</th>
                                <th>Gap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisons as $index => $comp)
                                @php
                                    $gap = $comp['gap'];
                                    $gapClass = $gap === null ? 'secondary' : ($gap > 0 ? 'danger' : ($gap < 0 ? 'success' : 'info'));
                                    $gapText = $gap === null ? 'N/A' : ($gap > 0 ? "↓ +{$gap}" : ($gap < 0 ? "↑ " . abs($gap) : "→ 0"));
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $comp['keyword']->keyword }}</strong>
                                        <input type="hidden" name="positions[{{ $index }}][keyword_id]" value="{{ $comp['keyword']->id }}">
                                    </td>
                                    <td>
                                        @if($comp['our_position'])
                                            <span class="badge badge-info badge-lg">{{ $comp['our_position'] }}</span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                               name="positions[{{ $index }}][competitor_position]"
                                               class="form-control form-control-sm competitor-position-input"
                                               value="{{ $comp['competitor_position'] }}"
                                               min="1"
                                               max="100"
                                               placeholder="Posición"
                                               style="width: 100px; display: inline-block;">
                                        <small class="text-muted ml-2">(1-100)</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $gapClass }} badge-lg gap-badge-{{ $comp['keyword']->id }}">{{ $gapText }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="form-group mt-3">
                    <label for="date">Fecha de la comparación:</label>
                    <input type="date"
                           name="date"
                           id="date"
                           class="form-control"
                           value="{{ $defaultDate ?? date('Y-m-d', strtotime('-1 day')) }}"
                           style="width: 200px;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Posiciones
                </button>
            </form>
        </div>
    </div>

    <!-- Comparación Completa de Keywords -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Comparación de Keywords
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Nuestra Posición</th>
                        <th>Posición Competidor</th>
                        <th>Gap</th>
                        <th>Estado</th>
                        <th>Última Comparación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comparisons as $comp)
                        @php
                            $gap = $comp['gap'];
                            $gapClass = $gap === null ? 'secondary' : ($gap > 0 ? 'danger' : ($gap < 0 ? 'success' : 'info'));
                            $gapText = $gap === null ? 'N/A' : ($gap > 0 ? "↓ +{$gap}" : ($gap < 0 ? "↑ " . abs($gap) : "→ 0"));
                            $statusText = $gap === null ? 'Sin comparar' : ($gap > 0 ? 'Competidor mejor' : ($gap < 0 ? 'Nosotros mejor' : 'Empate'));
                            $statusClass = $gap === null ? 'secondary' : ($gap > 0 ? 'danger' : ($gap < 0 ? 'success' : 'info'));
                        @endphp
                        <tr class="{{ $gap !== null && $gap > 0 ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $comp['keyword']->keyword }}</strong>
                                @if($comp['keyword']->target_url)
                                    <br><small class="text-muted">{{ Str::limit($comp['keyword']->target_url, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($comp['our_position'])
                                    <span class="badge badge-info badge-lg">{{ $comp['our_position'] }}</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($comp['competitor_position'])
                                    <span class="badge badge-success badge-lg">{{ $comp['competitor_position'] }}</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $gapClass }} badge-lg">{{ $gapText }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                @if($comp['date'])
                                    {{ $comp['date']->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('keywords.show', $comp['keyword']) }}" class="btn btn-sm btn-info" title="Ver keyword">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('keywords.dashboard', $comp['keyword']) }}" class="btn btn-sm btn-primary" title="Dashboard">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay keywords para comparar.
                                <a href="{{ route('keywords.create', ['site_id' => $site->id]) }}">Agregar keywords</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calcular gap automáticamente cuando se ingresa una posición
            $('.competitor-position-input').on('input', function() {
                var $row = $(this).closest('tr');
                var ourPosition = parseInt($row.find('.badge-info').text()) || null;
                var competitorPosition = parseInt($(this).val()) || null;
                var keywordId = $(this).closest('tr').find('input[type="hidden"]').val();

                if (ourPosition !== null && competitorPosition !== null) {
                    var gap = ourPosition - competitorPosition;
                    var gapClass = gap > 0 ? 'danger' : (gap < 0 ? 'success' : 'info');
                    var gapText = gap > 0 ? "↓ +" + gap : (gap < 0 ? "↑ " + Math.abs(gap) : "→ 0");

                    var $badge = $row.find('.gap-badge-' + keywordId);
                    $badge.removeClass('badge-secondary badge-danger badge-success badge-info')
                          .addClass('badge-' + gapClass)
                          .text(gapText);
                } else {
                    var $badge = $row.find('.gap-badge-' + keywordId);
                    $badge.removeClass('badge-danger badge-success badge-info')
                          .addClass('badge-secondary')
                          .text('N/A');
                }
            });
        });
    </script>
@stop

