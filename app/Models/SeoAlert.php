<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'type',
        'severity',
        'title',
        'message',
        'url',
        'keyword',
        'metadata',
        'is_read',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope para alertas no leídas
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope para alertas por severidad
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope para alertas por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para alertas no resueltas
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Marcar como leída
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Marcar como resuelta
     */
    public function markAsResolved()
    {
        $this->update([
            'resolved_at' => now(),
            'is_read' => true,
        ]);
    }

    /**
     * Obtener badge de severidad
     */
    public function getSeverityBadgeAttribute()
    {
        switch ($this->severity) {
            case 'critical':
                return '<span class="badge badge-danger">Crítica</span>';
            case 'warning':
                return '<span class="badge badge-warning">Advertencia</span>';
            case 'info':
                return '<span class="badge badge-info">Info</span>';
            default:
                return '<span class="badge badge-secondary">N/A</span>';
        }
    }

    /**
     * Obtener badge de tipo
     */
    public function getTypeBadgeAttribute()
    {
        $icons = [
            'position' => 'fa-chart-line',
            'traffic' => 'fa-chart-bar',
            'error' => 'fa-exclamation-circle',
            'indexing' => 'fa-search',
            'performance' => 'fa-tachometer-alt',
            'content' => 'fa-file-alt',
        ];

        $labels = [
            'position' => 'Posición',
            'traffic' => 'Tráfico',
            'error' => 'Error',
            'indexing' => 'Indexación',
            'performance' => 'Rendimiento',
            'content' => 'Contenido',
        ];

        $icon = $icons[$this->type] ?? 'fa-info-circle';
        $label = $labels[$this->type] ?? ucfirst($this->type);

        return '<span class="badge badge-secondary"><i class="fas ' . $icon . '"></i> ' . $label . '</span>';
    }
}
