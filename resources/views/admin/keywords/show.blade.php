@extends('adminlte::page')

@section('title', 'Detalles de Keyword')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Keyword: {{ $keyword->keyword }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('keywords.dashboard', $keyword) }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('keywords.index', ['site_id' => $keyword->site_id]) }}" class="btn btn-secondary">
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
                                <a href="{{ route('sites.show', $keyword->site) }}">
                                    {{ $keyword->site->nombre }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Keyword:</strong></td>
                            <td><strong>{{ $keyword->keyword }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>URL Objetivo:</strong></td>
                            <td>
                                @if($keyword->target_url)
                                    <a href="{{ $keyword->target_url }}" target="_blank">
                                        {{ $keyword->target_url }}
                                        <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Posición Actual:</strong></td>
                            <td>
                                @if($keyword->current_position)
                                    <span class="badge badge-info badge-lg">{{ $keyword->current_position }}</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Posición Anterior:</strong></td>
                            <td>
                                @if($keyword->previous_position)
                                    <span class="badge badge-secondary">{{ $keyword->previous_position }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cambio:</strong></td>
                            <td>
                                @php
                                    $change = $keyword->getPositionChangeBadge();
                                @endphp
                                @if($change['text'] !== 'N/A')
                                    <span class="badge badge-{{ $change['class'] }} badge-lg">
                                        <i class="fas {{ $change['icon'] }}"></i> {{ $change['text'] }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Última Verificación:</strong></td>
                            <td>
                                @if($keyword->last_checked)
                                    {{ $keyword->last_checked->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($keyword->is_active)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                        @if($keyword->notes)
                            <tr>
                                <td><strong>Notas:</strong></td>
                                <td>{{ $keyword->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Comparación de Posiciones -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Comparación de Posiciones</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hoy</span>
                                    <span class="info-box-number">
                                        @if($positionToday)
                                            {{ $positionToday }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-minus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ayer</span>
                                    <span class="info-box-number">
                                        @if($positionYesterday)
                                            {{ $positionYesterday }}
                                            @if($positionToday && $positionYesterday)
                                                @php
                                                    $change = $positionToday - $positionYesterday;
                                                @endphp
                                                <small class="text-{{ $change < 0 ? 'success' : ($change > 0 ? 'danger' : 'muted') }}">
                                                    ({{ $change < 0 ? '+' : '' }}{{ abs($change) }})
                                                </small>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar-week"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hace 7 días</span>
                                    <span class="info-box-number">
                                        @if($positionWeekAgo)
                                            {{ $positionWeekAgo }}
                                            @if($positionToday && $positionWeekAgo)
                                                @php
                                                    $change = $positionToday - $positionWeekAgo;
                                                @endphp
                                                <small class="text-{{ $change < 0 ? 'success' : ($change > 0 ? 'danger' : 'muted') }}">
                                                    ({{ $change < 0 ? '+' : '' }}{{ abs($change) }})
                                                </small>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('keywords.dashboard', $keyword) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-chart-line"></i> Ver Dashboard
                    </a>
                    <a href="{{ route('sites.dashboard', $keyword->site) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-globe"></i> Dashboard del Sitio
                    </a>
                    <form action="{{ route('keywords.update-positions') }}" method="POST" class="d-inline w-100">
                        @csrf
                        <input type="hidden" name="site_id" value="{{ $keyword->site_id }}">
                        <button type="submit" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-sync"></i> Actualizar Posición
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

