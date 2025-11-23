@extends('adminlte::page')

@section('title', 'Keywords')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Keywords Seguidas</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('keywords.create', ['site_id' => $siteId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Keyword
            </a>
            <form action="{{ route('keywords.update-positions') }}" method="POST" class="d-inline">
                @csrf
                @if($siteId)
                    <input type="hidden" name="site_id" value="{{ $siteId }}">
                @endif
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-sync"></i> Actualizar Posiciones
                </button>
            </form>
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
            <form method="GET" action="{{ route('keywords.index') }}">
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
                        <th>Keyword</th>
                        <th>Posición Actual</th>
                        <th>Cambio</th>
                        <th>Última Verificación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keywords as $keyword)
                        @php
                            $change = $keyword->getPositionChangeBadge();
                        @endphp
                        <tr>
                            <td>{{ $keyword->id }}</td>
                            <td>
                                <a href="{{ route('sites.show', $keyword->site) }}">
                                    {{ $keyword->site->nombre }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $keyword->keyword }}</strong>
                                @if($keyword->target_url)
                                    <br><small class="text-muted">{{ Str::limit($keyword->target_url, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($keyword->current_position)
                                    <span class="badge badge-info">{{ $keyword->current_position }}</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($change['text'] !== 'N/A')
                                    <span class="badge badge-{{ $change['class'] }}">
                                        <i class="fas {{ $change['icon'] }}"></i> {{ $change['text'] }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($keyword->last_checked)
                                    {{ $keyword->last_checked->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </td>
                            <td>
                                @if($keyword->is_active)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('keywords.show', $keyword) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('keywords.dashboard', $keyword) }}" class="btn btn-sm btn-primary" title="Dashboard">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay keywords registradas.
                                <a href="{{ route('keywords.create', ['site_id' => $siteId]) }}">Agregar primera keyword</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($keywords->hasPages())
            <div class="card-footer">
                {{ $keywords->links() }}
            </div>
        @endif
    </div>
@stop

