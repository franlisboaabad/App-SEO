@extends('adminlte::page')

@section('title', 'Lista de Sitios')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Gestión de Sitios Web</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Sitio
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

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover" id="table-sites">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Dominio Base</th>
                        <th>GSC Property</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sites as $site)
                        <tr>
                            <td>{{ $site->id }}</td>
                            <td>{{ $site->nombre }}</td>
                            <td>
                                <a href="https://{{ $site->dominio_base }}" target="_blank">
                                    {{ $site->dominio_base }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                            <td>{{ $site->gsc_property ?? 'No configurado' }}</td>
                            <td>
                                @if ($site->estado)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sites.show', $site) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sites.edit', $site) }}" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este sitio?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <p class="text-muted">No hay sitios registrados. <a href="{{ route('sites.create') }}">Crear uno nuevo</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#table-sites').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
        });
    </script>
@stop

