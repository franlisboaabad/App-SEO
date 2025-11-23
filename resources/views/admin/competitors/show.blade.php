@extends('adminlte::page')

@section('title', 'Detalles de Competidor')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Competidor: {{ $competitor->nombre }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('competitors.dashboard', ['site' => $competitor->site_id, 'competitor_id' => $competitor->id]) }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i> Dashboard de Competencia
            </a>
            <a href="{{ route('competitors.edit', $competitor) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('competitors.index', ['site_id' => $competitor->site_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información General</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td width="30%"><strong>Sitio:</strong></td>
                            <td>
                                <a href="{{ route('sites.show', $competitor->site) }}">
                                    {{ $competitor->site->nombre }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td><strong>{{ $competitor->nombre }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Dominio:</strong></td>
                            <td>
                                <a href="https://{{ $competitor->dominio_base }}" target="_blank">
                                    {{ $competitor->dominio_base }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>GSC Property:</strong></td>
                            <td>{{ $competitor->gsc_property ?? 'No configurado' }}</td>
                        </tr>
                        <tr>
                            <td><strong>GSC Credenciales:</strong></td>
                            <td>
                                @if($competitor->gsc_credentials)
                                    <span class="badge badge-success">Configurado</span>
                                @else
                                    <span class="badge badge-secondary">No configurado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($competitor->is_active)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        @if($competitor->notes)
                            <tr>
                                <td><strong>Notas:</strong></td>
                                <td>{{ $competitor->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Estadísticas de Comparación -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas de Comparación</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-key"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Keywords</span>
                                    <span class="info-box-number">{{ $totalKeywords }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-chart-bar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Comparadas</span>
                                    <span class="info-box-number">{{ $keywordsCompared }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Competidor Mejor</span>
                                    <span class="info-box-number">{{ $keywordsWhereCompetitorBetter }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-arrow-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nosotros Mejor</span>
                                    <span class="info-box-number">{{ $keywordsWhereWeBetter }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparaciones Recientes -->
            @if($comparisons->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Comparaciones Recientes (Últimos 7 días)</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Keyword</th>
                                    <th>Nuestra Posición</th>
                                    <th>Posición Competidor</th>
                                    <th>Gap</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparisons as $comparison)
                                    @php
                                        $gap = $comparison->position_gap;
                                        $gapClass = $gap > 0 ? 'danger' : ($gap < 0 ? 'success' : 'info');
                                        $gapText = $gap > 0 ? "↓ +{$gap}" : ($gap < 0 ? "↑ " . abs($gap) : "→ 0");
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('keywords.show', $comparison->keyword) }}">
                                                {{ $comparison->keyword->keyword }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($comparison->keyword->current_position)
                                                {{ $comparison->keyword->current_position }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($comparison->competitor_position)
                                                {{ $comparison->competitor_position }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($gap !== null)
                                                <span class="badge badge-{{ $gapClass }}">{{ $gapText }}</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $comparison->date->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('competitors.dashboard', ['site' => $competitor->site_id, 'competitor_id' => $competitor->id]) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-chart-line"></i> Ver Dashboard
                    </a>
                    <a href="{{ route('sites.show', $competitor->site) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-globe"></i> Ver Sitio
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

