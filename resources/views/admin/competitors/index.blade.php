@extends('adminlte::page')

@section('title', 'Competidores')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Competidores</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('competitors.create', ['site_id' => $siteId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Competidor
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

    <!-- Filtro por sitio -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('competitors.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <select name="site_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los sitios</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ $siteId == $site->id ? 'selected' : '' }}>
                                    {{ $site->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sitio</th>
                        <th>Nombre</th>
                        <th>Dominio</th>
                        <th>GSC Configurado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($competitors as $competitor)
                        <tr>
                            <td>{{ $competitor->id }}</td>
                            <td>
                                <a href="{{ route('sites.show', $competitor->site) }}">
                                    {{ $competitor->site->nombre }}
                                </a>
                            </td>
                            <td><strong>{{ $competitor->nombre }}</strong></td>
                            <td>
                                <a href="https://{{ $competitor->dominio_base }}" target="_blank">
                                    {{ $competitor->dominio_base }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                            <td>
                                @if($competitor->gsc_property && $competitor->gsc_credentials)
                                    <span class="badge badge-success">Sí</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                @if($competitor->is_active)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('competitors.show', $competitor) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('competitors.dashboard', ['site' => $competitor->site_id, 'competitor_id' => $competitor->id]) }}" class="btn btn-sm btn-primary" title="Dashboard">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <a href="{{ route('competitors.edit', $competitor) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay competidores registrados.
                                <a href="{{ route('competitors.create', ['site_id' => $siteId]) }}">Agregar primer competidor</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($competitors->hasPages())
            <div class="card-footer">
                {{ $competitors->links() }}
            </div>
        @endif
    </div>
@stop

