<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'keyword',
        'target_url',
        'current_position',
        'previous_position',
        'last_checked',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked' => 'date',
        'current_position' => 'integer',
        'previous_position' => 'integer',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Obtener posición desde métricas de GSC
     */
    public function getPositionFromMetrics($date = null)
    {
        $date = $date ?: Carbon::yesterday();

        $metric = SeoMetric::where('site_id', $this->site_id)
            ->where('keyword', $this->keyword)
            ->whereDate('date', $date)
            ->selectRaw('AVG(position) as avg_position')
            ->first();

        return $metric ? round($metric->avg_position, 1) : null;
    }

    /**
     * Obtener evolución de posiciones (últimos 30 días)
     */
    public function getPositionHistory($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);

        return SeoMetric::where('site_id', $this->site_id)
            ->where('keyword', $this->keyword)
            ->whereDate('date', '>=', $startDate)
            ->selectRaw('DATE(date) as date, AVG(position) as position, SUM(clicks) as clicks, SUM(impressions) as impressions')
            ->groupByRaw('DATE(date)')
            ->orderBy('date')
            ->get();
    }

    /**
     * Calcular cambio de posición
     */
    public function getPositionChange()
    {
        if (!$this->current_position || !$this->previous_position) {
            return null;
        }

        return $this->current_position - $this->previous_position;
    }

    /**
     * Obtener badge de cambio de posición
     */
    public function getPositionChangeBadge()
    {
        $change = $this->getPositionChange();

        if ($change === null) {
            return ['class' => 'secondary', 'text' => 'N/A', 'icon' => ''];
        }

        if ($change < 0) {
            // Subió (menor número = mejor posición)
            return ['class' => 'success', 'text' => '↑ ' . abs($change), 'icon' => 'fa-arrow-up'];
        } elseif ($change > 0) {
            // Bajó
            return ['class' => 'danger', 'text' => '↓ ' . $change, 'icon' => 'fa-arrow-down'];
        } else {
            return ['class' => 'info', 'text' => '→ 0', 'icon' => 'fa-minus'];
        }
    }

    /**
     * Scope para keywords activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relación con comparaciones de competidores
     */
    public function competitorComparisons()
    {
        return $this->hasMany(KeywordCompetitorComparison::class);
    }

    /**
     * Scope para filtrar por sitio
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
