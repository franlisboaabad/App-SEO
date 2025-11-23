@extends('adminlte::page')

@section('title', 'Dashboard Keyword - ' . $keyword->keyword)

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Dashboard Keyword: {{ $keyword->keyword }}</h1>
            <p class="text-muted">{{ $keyword->site->nombre }}</p>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('keywords.show', $keyword) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Resumen de Posiciones -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Posición Hoy</span>
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
                    <span class="info-box-text">Posición Ayer</span>
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

    <!-- Gráfico de Evolución -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Evolución de Posición (Últimos 30 días)
            </h3>
        </div>
        <div class="card-body">
            <canvas id="positionChart" height="80"></canvas>
        </div>
    </div>
@stop

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('positionChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Posición',
                    data: @json($chartData['positions']),
                    borderColor: 'rgb(60, 141, 188)',
                    backgroundColor: 'rgba(60, 141, 188, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            reverse: true, // Posición 1 arriba, mayor abajo
                            beginAtZero: false
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Posición'
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Fecha'
                        }
                    }]
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        });
    </script>
@stop

