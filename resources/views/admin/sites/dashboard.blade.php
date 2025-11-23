@extends('adminlte::page')

@section('title', 'Dashboard SEO - ' . $site->nombre)

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Dashboard SEO: {{ $site->nombre }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('sites.show', $site) }}" class="btn btn-secondary">
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

    <!-- Filtros de período -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('sites.dashboard', $site) }}" class="form-inline">
                <div class="form-group mr-2">
                    <label for="period" class="mr-2">Período:</label>
                    <select name="period" id="period" class="form-control" onchange="this.form.submit()">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>Últimos 90 días</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de métricas -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($summary->total_clicks ?? 0) }}</h3>
                    <p>Clics</p>
                </div>
                @if($previousSummary && $previousSummary->total_clicks > 0)
                    @php
                        $change = (($summary->total_clicks - $previousSummary->total_clicks) / $previousSummary->total_clicks) * 100;
                    @endphp
                    <small class="text-white">
                        {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs período anterior
                    </small>
                @endif
                <div class="icon">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($summary->total_impressions ?? 0) }}</h3>
                    <p>Impresiones</p>
                </div>
                @if($previousSummary && $previousSummary->total_impressions > 0)
                    @php
                        $change = (($summary->total_impressions - $previousSummary->total_impressions) / $previousSummary->total_impressions) * 100;
                    @endphp
                    <small class="text-white">
                        {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs período anterior
                    </small>
                @endif
                <div class="icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format(($summary->avg_ctr ?? 0) * 100, 2) }}%</h3>
                    <p>CTR Promedio</p>
                </div>
                @if($previousSummary && $previousSummary->avg_ctr > 0)
                    @php
                        $change = ((($summary->avg_ctr - $previousSummary->avg_ctr) / $previousSummary->avg_ctr) * 100);
                    @endphp
                    <small class="text-white">
                        {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs período anterior
                    </small>
                @endif
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($summary->avg_position ?? 0, 1) }}</h3>
                    <p>Posición Promedio</p>
                </div>
                @if($previousSummary && $previousSummary->avg_position > 0)
                    @php
                        $change = ((($summary->avg_position - $previousSummary->avg_position) / $previousSummary->avg_position) * 100);
                    @endphp
                    <small class="text-white">
                        {{ $change < 0 ? '+' : '' }}{{ number_format(abs($change), 1) }}% vs período anterior
                        <small>({{ $change < 0 ? 'mejor' : 'peor' }})</small>
                    </small>
                @endif
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Clics e Impresiones</h3>
                </div>
                <div class="card-body">
                    <canvas id="clicksImpressionsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">CTR y Posición</h3>
                </div>
                <div class="card-body">
                    <canvas id="ctrPositionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top URLs y Keywords -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 URLs por Clics</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>URL</th>
                                <th>Clics</th>
                                <th>Impresiones</th>
                                <th>Posición</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUrls as $url)
                                <tr>
                                    <td>
                                        <a href="{{ $url->url }}" target="_blank" title="{{ $url->url }}">
                                            {{ Str::limit($url->url, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($url->total_clicks) }}</td>
                                    <td>{{ number_format($url->total_impressions) }}</td>
                                    <td>{{ number_format($url->avg_position, 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Keywords por Clics</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Clics</th>
                                <th>Impresiones</th>
                                <th>Posición</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topKeywords as $keyword)
                                <tr>
                                    <td><strong>{{ $keyword->keyword }}</strong></td>
                                    <td>{{ number_format($keyword->total_clicks) }}</td>
                                    <td>{{ number_format($keyword->total_impressions) }}</td>
                                    <td>{{ number_format($keyword->avg_position, 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Páginas con errores SEO -->
    @if($pagesWithErrors->count() > 0)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h3 class="card-title">Páginas con Errores SEO</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>Errores</th>
                                    <th>Score SEO</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagesWithErrors as $audit)
                                    <tr>
                                        <td>
                                            <a href="{{ $audit->url }}" target="_blank">
                                                {{ Str::limit($audit->url, 60) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">{{ count($audit->result->errors ?? []) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $audit->result->seo_score >= 70 ? 'success' : ($audit->result->seo_score >= 50 ? 'warning' : 'danger') }}">
                                                {{ $audit->result->seo_score ?? 0 }}/100
                                            </span>
                                        </td>
                                        <td>{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver
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
    @endif
@stop

@section('js')
    <script>
        // Datos para gráficos
        const dailyData = @json($dailyMetrics);
        const dates = dailyData.map(item => item.date);
        const clicks = dailyData.map(item => parseInt(item.clicks) || 0);
        const impressions = dailyData.map(item => parseInt(item.impressions) || 0);
        const ctr = dailyData.map(item => parseFloat(item.ctr) * 100 || 0);
        const position = dailyData.map(item => parseFloat(item.position) || 0);

        // Gráfico de Clics e Impresiones
        const ctx1 = document.getElementById('clicksImpressionsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Clics',
                        data: clicks,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Impresiones',
                        data: impressions,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de CTR y Posición
        const ctx2 = document.getElementById('ctrPositionChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'CTR (%)',
                        data: ctr,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        yAxisID: 'y',
                        tension: 0.1
                    },
                    {
                        label: 'Posición',
                        data: position,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    </script>
@stop

