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
     * Relación con keywords
     */
    public function keywords()
    {
        return $this->hasMany(\App\Models\Keyword::class);
    }

    /**
     * Relación con tareas SEO
     */
    public function seoTasks()
    {
        return $this->hasMany(\App\Models\SeoTask::class);
    }

    /**
     * Relación con competidores
     */
    public function competitors()
    {
        return $this->hasMany(\App\Models\Competitor::class);
    }

    /**
     * Relación con alertas
     */
    public function alerts()
    {
        return $this->hasMany(\App\Models\SeoAlert::class);
    }

    /**
     * Relación con investigación de keywords
     */
    public function keywordResearch()
    {
        return $this->hasMany(\App\Models\KeywordResearch::class);
    }

    /**
     * Relación con análisis de SERP
     */
    public function serpAnalyses()
    {
        return $this->hasMany(\App\Models\SerpAnalysis::class);
    }

    /**
     * Scope para sitios activos
     */
    public function scopeActive($query)
    {
        return $query->where('estado', true);
    }
}
