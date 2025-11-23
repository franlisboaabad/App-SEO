<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Auditoría SEO</title>
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
        .score-box {
            text-align: center;
            padding: 30px;
            margin: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .score-box .score-number {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
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
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .error-list, .warning-list {
            margin: 10px 0;
        }
        .error-list li, .warning-list li {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid;
        }
        .error-list li {
            border-color: #dc3545;
            background-color: #ffe6e6;
        }
        .warning-list li {
            border-color: #ffc107;
            background-color: #fff9e6;
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
        <h1>Reporte de Auditoría SEO</h1>
        <p>{{ $audit->site->nombre }} - {{ \Carbon\Carbon::parse($audit->created_at)->format('d/m/Y H:i:s') }}</p>
    </div>

    @if($audit->result)
        @php
            $result = $audit->result;
            $score = $result ? $result->seo_score : 0;
        @endphp

        <!-- Score SEO -->
        <div class="score-box">
            <div style="font-size: 18px; margin-bottom: 10px;">Score SEO</div>
            <div class="score-number">{{ $score }}/100</div>
            <div style="font-size: 14px; margin-top: 10px;">
                @if($score >= 70)
                    Excelente - Tu página tiene un buen SEO
                @elseif($score >= 50)
                    Regular - Hay aspectos que mejorar
                @else
                    Necesita Mejoras - Hay varios problemas SEO
                @endif
            </div>
        </div>

        <!-- Información General -->
        <div class="info-section">
            <h2>Información General</h2>
            <table>
                <tr>
                    <td width="30%"><strong>URL Auditada:</strong></td>
                    <td>{{ $audit->url }}</td>
                </tr>
                <tr>
                    <td><strong>Status Code:</strong></td>
                    <td>
                        @if($result->status_code)
                            <span class="badge {{ $result->status_code >= 400 ? 'badge-danger' : 'badge-success' }}">
                                {{ $result->status_code }}
                            </span>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>TTFB:</strong></td>
                    <td>
                        @if($result->ttfb)
                            {{ number_format($result->ttfb, 3) }}s
                            @if($result->ttfb > 0.6)
                                <span class="badge badge-warning">Lento</span>
                            @else
                                <span class="badge badge-success">Rápido</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- SEO On-Page -->
        <div class="info-section">
            <h2>SEO On-Page</h2>
            <table>
                <tr>
                    <td width="30%"><strong>Title:</strong></td>
                    <td>{{ $result->title ?? 'No encontrado' }}</td>
                </tr>
                <tr>
                    <td><strong>Meta Description:</strong></td>
                    <td>{{ $result->meta_description ?? 'No encontrada' }}</td>
                </tr>
                <tr>
                    <td><strong>H1:</strong></td>
                    <td>
                        <span class="badge {{ $result->h1_count == 1 ? 'badge-success' : ($result->h1_count == 0 ? 'badge-danger' : 'badge-warning') }}">
                            {{ $result->h1_count }} encontrado(s)
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>H2:</strong></td>
                    <td>{{ $result->h2_count }}</td>
                </tr>
                <tr>
                    <td><strong>H3:</strong></td>
                    <td>{{ $result->h3_count }}</td>
                </tr>
                <tr>
                    <td><strong>Imágenes sin ALT:</strong></td>
                    <td>
                        @if($result->images_total ?? 0 > 0)
                            <span class="badge {{ ($result->images_without_alt ?? 0) > 0 ? 'badge-warning' : 'badge-success' }}">
                                {{ $result->images_without_alt ?? 0 }} / {{ $result->images_total }}
                            </span>
                        @else
                            <span class="badge badge-secondary">0</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Canonical:</strong></td>
                    <td>{{ $result->canonical ?? 'No encontrado' }}</td>
                </tr>
                <tr>
                    <td><strong>Robots Meta:</strong></td>
                    <td>{{ $result->robots_meta ?? 'No especificado' }}</td>
                </tr>
            </table>
        </div>

        <!-- Links -->
        <div class="info-section">
            <h2>Análisis de Links</h2>
            <table>
                <tr>
                    <td width="30%"><strong>Links Internos:</strong></td>
                    <td>{{ $result->internal_links_count }}</td>
                </tr>
                <tr>
                    <td><strong>Links Externos:</strong></td>
                    <td>{{ $result->external_links_count }}</td>
                </tr>
                <tr>
                    <td><strong>Links Rotos:</strong></td>
                    <td>
                        @if($result->broken_links_count > 0)
                            <span class="badge badge-danger">{{ $result->broken_links_count }}</span>
                        @else
                            <span class="badge badge-success">0</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Errores y Advertencias -->
        @if(count($result->errors ?? []) > 0 || count($result->warnings ?? []) > 0)
            <div class="info-section">
                <h2>Errores y Advertencias</h2>

                @if(count($result->errors ?? []) > 0)
                    <h3 style="color: #dc3545; margin-top: 15px; margin-bottom: 10px;">Errores ({{ count($result->errors) }})</h3>
                    <ul class="error-list">
                        @foreach($result->errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(count($result->warnings ?? []) > 0)
                    <h3 style="color: #ffc107; margin-top: 15px; margin-bottom: 10px;">Advertencias ({{ count($result->warnings) }})</h3>
                    <ul class="warning-list">
                        @foreach($result->warnings as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @else
            <div class="info-section" style="background-color: #d4edda; border-color: #28a745;">
                <h2 style="color: #28a745;">¡Excelente!</h2>
                <p>No se encontraron errores ni advertencias en esta auditoría.</p>
            </div>
        @endif
    @else
        <div class="info-section" style="background-color: #fff3cd; border-color: #ffc107;">
            <h2 style="color: #856404;">Auditoría sin Resultados</h2>
            <p>Esta auditoría no tiene resultados disponibles.</p>
            @if($audit->status == 'failed')
                <p><strong>Error:</strong> {{ $audit->error_message }}</p>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }} | SEO Dashboard Tool</p>
    </div>
</body>
</html>

