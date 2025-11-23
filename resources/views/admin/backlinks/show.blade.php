@extends('adminlte::page')

@section('title', 'Detalle de Backlink')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-link"></i> Detalle de Backlink</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('backlinks.edit', $backlink->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('backlinks.index', ['site_id' => $backlink->site_id]) }}" class="btn btn-secondary">
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
                    <h3 class="card-title">Información del Backlink</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Sitio:</th>
                            <td>
                                <a href="{{ route('sites.show', $backlink->site->id) }}">
                                    {{ $backlink->site->nombre }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Dominio Fuente:</th>
                            <td><strong>{{ $backlink->source_domain }}</strong></td>
                        </tr>
                        <tr>
                            <th>URL Fuente:</th>
                            <td>
                                <a href="{{ $backlink->source_url }}" target="_blank" rel="noopener">
                                    {{ $backlink->source_url }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>URL Destino:</th>
                            <td>
                                <a href="{{ $backlink->target_url }}" target="_blank" rel="noopener">
                                    {{ $backlink->target_url }}
                                    <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Anchor Text:</th>
                            <td>{{ $backlink->anchor_text ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Enlace:</th>
                            <td>{!! $backlink->link_type_badge !!}</td>
                        </tr>
                        <tr>
                            <th>Fuente:</th>
                            <td>{!! $backlink->source_type_badge !!}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($backlink->is_toxic)
                                    <span class="badge badge-danger">Tóxico</span>
                                    @if($backlink->toxic_reason)
                                        <br><small class="text-muted">{{ $backlink->toxic_reason }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Domain Authority:</th>
                            <td>{{ $backlink->domain_authority ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Page Authority:</th>
                            <td>{{ $backlink->page_authority ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Primera Detección:</th>
                            <td>{{ $backlink->first_seen ? $backlink->first_seen->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Última Detección:</th>
                            <td>{{ $backlink->last_seen ? $backlink->last_seen->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        @if($backlink->notes)
                            <tr>
                                <th>Notas:</th>
                                <td>{{ $backlink->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones</h3>
                </div>
                <div class="card-body">
                    <a href="{{ $backlink->source_url }}" target="_blank" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-external-link-alt"></i> Ver URL Fuente
                    </a>
                    <a href="{{ $backlink->target_url }}" target="_blank" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-external-link-alt"></i> Ver URL Destino
                    </a>
                    <a href="{{ route('backlinks.edit', $backlink->id) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i> Editar Backlink
                    </a>
                    <form action="{{ route('backlinks.destroy', $backlink->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este backlink?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

