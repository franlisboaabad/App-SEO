<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'url',
        'status',
        'error_message',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Relación con los resultados de la auditoría
     */
    public function result()
    {
        return $this->hasOne(AuditResult::class);
    }

    /**
     * Relación con tareas generadas
     */
    public function seoTasks()
    {
        return $this->hasMany(\App\Models\SeoTask::class);
    }

    /**
     * Scope para auditorías completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para auditorías pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para auditorías fallidas
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
