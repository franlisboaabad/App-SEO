<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'nombre',
        'dominio_base',
        'gsc_property',
        'gsc_credentials',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gsc_credentials' => 'array',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Relación con comparaciones de keywords
     */
    public function keywordComparisons()
    {
        return $this->hasMany(KeywordCompetitorComparison::class);
    }

    /**
     * Scope para competidores activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por sitio
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
