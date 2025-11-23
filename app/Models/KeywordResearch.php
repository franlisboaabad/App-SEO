<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordResearch extends Model
{
    use HasFactory;

    protected $table = 'keyword_research';

    protected $fillable = [
        'site_id',
        'keyword',
        'search_volume',
        'difficulty',
        'cpc',
        'intent',
        'current_position',
        'clicks',
        'impressions',
        'ctr',
        'source',
        'notes',
        'is_tracked',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'difficulty' => 'decimal:2',
        'cpc' => 'decimal:2',
        'current_position' => 'integer',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'decimal:2',
        'is_tracked' => 'boolean',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope para keywords no trackeadas
     */
    public function scopeUntracked($query)
    {
        return $query->where('is_tracked', false);
    }

    /**
     * Scope para filtrar por fuente
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Obtener badge de intención
     */
    public function getIntentBadgeAttribute()
    {
        $badges = [
            'informational' => ['class' => 'info', 'label' => 'Informativa'],
            'navigational' => ['class' => 'primary', 'label' => 'Navegacional'],
            'transactional' => ['class' => 'success', 'label' => 'Transaccional'],
            'commercial' => ['class' => 'warning', 'label' => 'Comercial'],
        ];

        $intent = $badges[$this->intent] ?? ['class' => 'secondary', 'label' => 'N/A'];
        return '<span class="badge badge-' . $intent['class'] . '">' . $intent['label'] . '</span>';
    }

    /**
     * Obtener badge de dificultad
     */
    public function getDifficultyBadgeAttribute()
    {
        if (!$this->difficulty) {
            return '<span class="badge badge-secondary">N/A</span>';
        }

        if ($this->difficulty < 30) {
            return '<span class="badge badge-success">Fácil (' . $this->difficulty . ')</span>';
        } elseif ($this->difficulty < 70) {
            return '<span class="badge badge-warning">Media (' . $this->difficulty . ')</span>';
        } else {
            return '<span class="badge badge-danger">Difícil (' . $this->difficulty . ')</span>';
        }
    }
}
