<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SeoTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'seo_audit_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'url',
        'priority',
        'status',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Relación con la auditoría (si fue generada automáticamente)
     */
    public function seoAudit()
    {
        return $this->belongsTo(SeoAudit::class);
    }

    /**
     * Usuario que creó la tarea
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario asignado
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope para tareas pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para tareas en progreso
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope para tareas completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para filtrar por sitio
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope para filtrar por prioridad
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope para tareas vencidas
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now());
    }

    /**
     * Marcar tarea como completada
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);
    }

    /**
     * Obtener badge de prioridad
     */
    public function getPriorityBadge()
    {
        $badges = [
            'low' => ['class' => 'secondary', 'text' => 'Baja'],
            'medium' => ['class' => 'info', 'text' => 'Media'],
            'high' => ['class' => 'warning', 'text' => 'Alta'],
            'critical' => ['class' => 'danger', 'text' => 'Crítica'],
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    /**
     * Obtener badge de estado
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => ['class' => 'warning', 'text' => 'Pendiente'],
            'in_progress' => ['class' => 'info', 'text' => 'En Progreso'],
            'completed' => ['class' => 'success', 'text' => 'Completada'],
            'cancelled' => ['class' => 'secondary', 'text' => 'Cancelada'],
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Verificar si está vencida
     */
    public function isOverdue()
    {
        return $this->due_date
            && $this->due_date < Carbon::now()
            && !in_array($this->status, ['completed', 'cancelled']);
    }
}
