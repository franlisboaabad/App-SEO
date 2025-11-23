<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'url',
        'keyword',
        'device',
        'date',
        'clicks',
        'impressions',
        'ctr',
        'position',
    ];

    protected $casts = [
        'date' => 'date',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'decimal:4',
        'position' => 'decimal:2',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope para filtrar por sitio
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope para filtrar por dispositivo
     */
    public function scopeDevice($query, $device)
    {
        return $query->where('device', $device);
    }
}
