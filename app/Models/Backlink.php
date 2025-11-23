<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'source_domain',
        'source_url',
        'target_url',
        'anchor_text',
        'link_type',
        'first_seen',
        'last_seen',
        'domain_authority',
        'page_authority',
        'source_type',
        'is_toxic',
        'toxic_reason',
        'notes',
    ];

    protected $casts = [
        'first_seen' => 'date',
        'last_seen' => 'date',
        'is_toxic' => 'boolean',
        'domain_authority' => 'integer',
        'page_authority' => 'integer',
    ];

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Obtener badge de tipo de enlace
     */
    public function getLinkTypeBadgeAttribute()
    {
        $badges = [
            'dofollow' => ['class' => 'success', 'text' => 'Dofollow'],
            'nofollow' => ['class' => 'secondary', 'text' => 'Nofollow'],
            'sponsored' => ['class' => 'warning', 'text' => 'Sponsored'],
            'ugc' => ['class' => 'info', 'text' => 'UGC'],
        ];

        $badge = $badges[$this->link_type] ?? $badges['dofollow'];
        return '<span class="badge badge-'.$badge['class'].'">'.$badge['text'].'</span>';
    }

    /**
     * Obtener badge de fuente
     */
    public function getSourceTypeBadgeAttribute()
    {
        $badges = [
            'gsc' => ['class' => 'primary', 'text' => 'Google Search Console'],
            'manual' => ['class' => 'info', 'text' => 'Manual'],
            'api_ahrefs' => ['class' => 'success', 'text' => 'Ahrefs API'],
            'api_semrush' => ['class' => 'warning', 'text' => 'SEMrush API'],
            'api_moz' => ['class' => 'secondary', 'text' => 'Moz API'],
        ];

        $badge = $badges[$this->source_type] ?? $badges['manual'];
        return '<span class="badge badge-'.$badge['class'].'">'.$badge['text'].'</span>';
    }

    /**
     * Scope para backlinks tóxicos
     */
    public function scopeToxic($query)
    {
        return $query->where('is_toxic', true);
    }

    /**
     * Scope para backlinks dofollow
     */
    public function scopeDofollow($query)
    {
        return $query->where('link_type', 'dofollow');
    }

    /**
     * Scope para un sitio específico
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
