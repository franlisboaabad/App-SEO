@extends('adminlte::page')

@section('title', 'Detalles de Tarea')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1>Tarea: {{ $task->title }}</h1>
        </div>
        <div class="col-md text-right">
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('tasks.kanban', ['site_id' => $task->site_id]) }}" class="btn btn-secondary">
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
                                <a href="{{ route('sites.show', $task->site) }}">
                                    {{ $task->site->nombre }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Título:</strong></td>
                            <td><strong>{{ $task->title }}</strong></td>
                        </tr>
                        @if($task->description)
                            <tr>
                                <td><strong>Descripción:</strong></td>
                                <td>{{ $task->description }}</td>
                            </tr>
                        @endif
                        @if($task->url)
                            <tr>
                                <td><strong>URL:</strong></td>
                                <td>
                                    <a href="{{ $task->url }}" target="_blank">
                                        {{ $task->url }}
                                        <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Prioridad:</strong></td>
                            <td>
                                @php
                                    $priorityBadge = $task->getPriorityBadge();
                                @endphp
                                <span class="badge badge-{{ $priorityBadge['class'] }} badge-lg">
                                    {{ $priorityBadge['text'] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @php
                                    $statusBadge = $task->getStatusBadge();
                                @endphp
                                <span class="badge badge-{{ $statusBadge['class'] }} badge-lg">
                                    {{ $statusBadge['text'] }}
                                </span>
                            </td>
                        </tr>
                        @if($task->assignee)
                            <tr>
                                <td><strong>Asignado a:</strong></td>
                                <td>{{ $task->assignee->name }}</td>
                            </tr>
                        @endif
                        @if($task->due_date)
                            <tr>
                                <td><strong>Fecha Límite:</strong></td>
                                <td>
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if($task->isOverdue())
                                        <span class="badge badge-danger ml-2">Vencida</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if($task->completed_at)
                            <tr>
                                <td><strong>Completada el:</strong></td>
                                <td>{{ $task->completed_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                        @if($task->creator)
                            <tr>
                                <td><strong>Creada por:</strong></td>
                                <td>{{ $task->creator->name }}</td>
                            </tr>
                        @endif
                        @if($task->seoAudit)
                            <tr>
                                <td><strong>Auditoría relacionada:</strong></td>
                                <td>
                                    <a href="{{ route('audits.show', $task->seoAudit) }}">
                                        Ver auditoría #{{ $task->seoAudit->id }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    @if($task->status !== 'pending')
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-clock"></i> Mover a Pendiente
                            </button>
                        </form>
                    @endif
                    @if($task->status !== 'in_progress')
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="fas fa-spinner"></i> Mover a En Progreso
                            </button>
                        </form>
                    @endif
                    @if($task->status !== 'completed')
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Marcar como Completada
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('sites.show', $task->site) }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-globe"></i> Ver Sitio
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

