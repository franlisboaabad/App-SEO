@extends('adminlte::page')

@section('title', 'Detalles del Sitio')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Detalles del Sitio: {{ $site->nombre }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('sites.edit', $site) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('sites.index') }}" class="btn btn-secondary">
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
                            <th width="200">ID</th>
                            <td>{{ $site->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>{{ $site->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Dominio Base</th>
                            <td>
                                <a href="https://{{ $site->dominio_base }}" target="_blank">
                                    {{ $site->dominio_base }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>GSC Property</th>
                            <td>{{ $site->gsc_property ?? 'No configurado' }}</td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                @if ($site->estado)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación</th>
                            <td>{{ $site->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización</th>
                            <td>{{ $site->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($site->gsc_credentials)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Credenciales GSC Configuradas</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Las credenciales están configuradas y almacenadas de forma segura.
                        </div>
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
                    <a href="{{ route('sites.dashboard', $site) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-chart-line"></i> Ver Dashboard SEO
                    </a>
                    <button type="button" class="btn btn-info btn-block mb-2" data-toggle="modal" data-target="#auditModal">
                        <i class="fas fa-search"></i> Ejecutar Auditoría
                    </button>
                    <a href="{{ route('sites.audits', $site) }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-history"></i> Historial de Auditorías
                    </a>
                    <a href="#" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-sync"></i> Sincronizar Métricas
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Métricas:</strong>
                        <span class="badge badge-info">{{ $site->seoMetrics()->count() ?? 0 }}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Auditorías:</strong>
                        <span class="badge badge-info">{{ $site->seoAudits()->count() ?? 0 }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

<!-- Modal para ejecutar auditoría -->
<div class="modal fade" id="auditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ejecutar Auditoría SEO</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('sites.audit', $site) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="audit_url">URL a auditar</label>
                        <input type="url"
                               name="url"
                               id="audit_url"
                               class="form-control"
                               placeholder="https://{{ $site->dominio_base }}"
                               value="https://{{ $site->dominio_base }}"
                               required>
                        <small class="form-text text-muted">Ingrese la URL completa de la página a auditar</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Ejecutar Auditoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
    <script>
        console.log('Sitio: {{ $site->nombre }}');
    </script>
@stop

