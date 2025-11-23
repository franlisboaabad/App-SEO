<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'dominio_base',
        'gsc_property',
        'gsc_credentials',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'gsc_credentials' => 'array', // Para manejar JSON
    ];

    /**
     * Relación con métricas SEO
     */
    public function seoMetrics()
    {
        return $this->hasMany(\App\Models\SeoMetric::class);
    }

    /**
     * Relación con auditorías SEO
     */
    public function seoAudits()
    {
        return $this->hasMany(\App\Models\SeoAudit::class);
    }

    /**
     * Scope para sitios activos
     */
    public function scopeActive($query)
    {
        return $query->where('estado', true);
    }
}
