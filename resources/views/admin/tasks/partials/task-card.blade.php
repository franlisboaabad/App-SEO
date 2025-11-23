@php
    $priorityBadge = $task->getPriorityBadge();
    $statusBadge = $task->getStatusBadge();
    $isOverdue = $task->isOverdue();
@endphp

<div class="card task-card task-priority-{{ $task->priority }} {{ $isOverdue ? 'task-overdue' : '' }}" data-task-id="{{ $task->id }}">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h6 class="mb-1">
                    <a href="{{ route('tasks.show', $task) }}" class="text-dark">
                        {{ Str::limit($task->title, 40) }}
                    </a>
                </h6>
                <small class="text-muted">
                    <a href="{{ route('sites.show', $task->site) }}" class="text-muted">
                        {{ $task->site->nombre }}
                    </a>
                </small>
            </div>
            <span class="badge badge-{{ $priorityBadge['class'] }} badge-sm">
                {{ $priorityBadge['text'] }}
            </span>
        </div>

        @if($task->description)
            <p class="mb-2 text-sm" style="font-size: 0.85rem;">
                {{ Str::limit($task->description, 80) }}
            </p>
        @endif

        @if($task->url)
            <div class="mb-2">
                <a href="{{ $task->url }}" target="_blank" class="text-sm text-info">
                    <i class="fas fa-external-link-alt"></i> Ver URL
                </a>
            </div>
        @endif

        @if($task->assignee)
            <div class="mb-2">
                <small class="text-muted">
                    <i class="fas fa-user"></i> {{ $task->assignee->name }}
                </small>
            </div>
        @endif

        @if($task->due_date)
            <div class="mb-2">
                <small class="text-{{ $isOverdue ? 'danger' : 'muted' }}">
                    <i class="fas fa-calendar"></i>
                    {{ $task->due_date->format('d/m/Y') }}
                    @if($isOverdue)
                        <span class="badge badge-danger badge-sm">Vencida</span>
                    @endif
                </small>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="btn-group btn-group-sm" role="group">
                @if($task->status !== 'pending')
                    <button type="button"
                            class="btn btn-sm btn-warning change-status-btn"
                            data-task-id="{{ $task->id }}"
                            data-status="pending"
                            title="Mover a Pendiente">
                        <i class="fas fa-clock"></i>
                    </button>
                @endif
                @if($task->status !== 'in_progress')
                    <button type="button"
                            class="btn btn-sm btn-info change-status-btn"
                            data-task-id="{{ $task->id }}"
                            data-status="in_progress"
                            title="Mover a En Progreso">
                        <i class="fas fa-spinner"></i>
                    </button>
                @endif
                @if($task->status !== 'completed')
                    <button type="button"
                            class="btn btn-sm btn-success change-status-btn"
                            data-task-id="{{ $task->id }}"
                            data-status="completed"
                            title="Marcar como Completada">
                        <i class="fas fa-check"></i>
                    </button>
                @endif
            </div>
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-secondary" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </div>
</div>

