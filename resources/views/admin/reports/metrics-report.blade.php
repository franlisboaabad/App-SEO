<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Métricas - {{ $site->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background-color: #3c8dbc;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #3c8dbc;
        }
        .info-section h2 {
            color: #3c8dbc;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #3c8dbc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        table th {
            background-color: #3c8dbc;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #3c8dbc;
        }
        .stat-box .label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Métricas SEO</h1>
        <p>{{ $site->nombre }} - Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <!-- Resumen -->
    @if($summary)
        <div class="info-section">
            <h2>Resumen General</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="number">{{ number_format($summary->total_clicks ?? 0) }}</div>
                    <div class="label">Total de Clics</div>
                </div>
                <div class="stat-box">
                    <div class="number">{{ number_format($summary->total_impressions ?? 0) }}</div>
                    <div class="label">Total de Impresiones</div>
                </div>
                <div class="stat-box">
                    <div class="number">{{ number_format(($summary->avg_ctr ?? 0) * 100, 2) }}%</div>
                    <div class="label">CTR Promedio</div>
                </div>
                <div class="stat-box">
                    <div class="number">{{ number_format($summary->avg_position ?? 0, 1) }}</div>
                    <div class="label">Posición Promedio</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Métricas Diarias -->
    @if($metrics->count() > 0)
        <div class="info-section">
            <h2>Métricas Diarias</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Clics</th>
                        <th>Impresiones</th>
                        <th>CTR</th>
                        <th>Posición</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metrics as $metric)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($metric->date)->format('d/m/Y') }}</td>
                            <td>{{ number_format($metric->clicks) }}</td>
                            <td>{{ number_format($metric->impressions) }}</td>
                            <td>{{ number_format($metric->ctr * 100, 2) }}%</td>
                            <td>{{ number_format($metric->position, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="info-section">
            <p>No hay métricas disponibles para el período seleccionado.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }} | SEO Dashboard Tool</p>
    </div>
</body>
</html>

