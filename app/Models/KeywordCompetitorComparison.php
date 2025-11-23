<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordCompetitorComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'competitor_id',
        'competitor_position',
        'date',
        'position_gap',
    ];

    protected $casts = [
        'date' => 'date',
        'competitor_position' => 'integer',
        'position_gap' => 'integer',
    ];

    /**
     * Relación con keyword
     */
    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    /**
     * Relación con competidor
     */
    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Scope para filtrar por keyword
     */
    public function scopeForKeyword($query, $keywordId)
    {
        return $query->where('keyword_id', $keywordId);
    }

    /**
     * Scope para filtrar por competidor
     */
    public function scopeForCompetitor($query, $competitorId)
    {
        return $query->where('competitor_id', $competitorId);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
