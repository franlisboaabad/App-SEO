<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerpAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'keyword_id',
        'keyword',
        'position',
        'url',
        'title',
        'description',
        'display_url',
        'competitors',
        'features',
        'suggestions',
        'analysis_date',
    ];

    protected $casts = [
        'competitors' => 'array',
        'features' => 'array',
        'analysis_date' => 'date',
        'position' => 'integer',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Relación con la keyword (opcional)
     */
    public function keywordRelation()
    {
        return $this->belongsTo(Keyword::class, 'keyword_id');
    }

    /**
     * Alias para mantener compatibilidad
     */
    public function keyword()
    {
        return $this->keywordRelation();
    }

    /**
     * Scope para análisis recientes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('analysis_date', '>=', now()->subDays($days));
    }

    /**
     * Scope para un sitio específico
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Obtener badge de posición
     */
    public function getPositionBadgeAttribute()
    {
        if (!$this->position) {
            return '<span class="badge badge-secondary">No rankea</span>';
        }

        if ($this->position <= 3) {
            return '<span class="badge badge-success">Posición ' . $this->position . '</span>';
        } elseif ($this->position <= 10) {
            return '<span class="badge badge-info">Posición ' . $this->position . '</span>';
        } elseif ($this->position <= 20) {
            return '<span class="badge badge-warning">Posición ' . $this->position . '</span>';
        } else {
            return '<span class="badge badge-danger">Posición ' . $this->position . '</span>';
        }
    }

    /**
     * Obtener fecha de análisis de forma segura
     */
    public function getAnalysisDateFormattedAttribute()
    {
        if ($this->analysis_date) {
            return $this->analysis_date->format('d/m/Y');
        }

        if ($this->created_at) {
            return $this->created_at->format('d/m/Y');
        }

        return 'N/A';
    }
}
