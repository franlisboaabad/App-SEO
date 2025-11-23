<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_audit_id',
        'title',
        'meta_description',
        'h1_count',
        'h2_count',
        'h3_count',
        'images_without_alt',
        'images_total',
        'canonical',
        'robots_meta',
        'word_count',
        'keyword_density',
        'content_suggestions',
        'internal_links_count',
        'external_links_count',
        'broken_links_count',
        'internal_links',
        'external_links',
        'broken_links',
        'ttfb',
        'status_code',
        'errors',
        'warnings',
    ];

    protected $casts = [
        'errors' => 'array',
        'warnings' => 'array',
        'internal_links' => 'array',
        'external_links' => 'array',
        'broken_links' => 'array',
        'keyword_density' => 'array',
        'content_suggestions' => 'array',
        'ttfb' => 'decimal:3',
    ];

    /**
     * Relación con la auditoría
     */
    public function audit()
    {
        return $this->belongsTo(SeoAudit::class, 'seo_audit_id');
    }

    /**
     * Calcular score SEO (0-100)
     */
    public function getSeoScoreAttribute()
    {
        $score = 100;

        // Penalizaciones
        if ($this->h1_count == 0) $score -= 10;
        if ($this->h1_count > 1) $score -= 5;
        if (empty($this->title)) $score -= 15;
        if (empty($this->meta_description)) $score -= 10;
        if ($this->images_without_alt > 0) $score -= ($this->images_without_alt * 2);
        if ($this->broken_links_count > 0) $score -= ($this->broken_links_count * 3);
        if ($this->status_code >= 400) $score -= 20;

        return max(0, $score);
    }
}
