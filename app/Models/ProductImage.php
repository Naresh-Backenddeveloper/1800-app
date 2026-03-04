<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'url',
        'path',
        'is_main',
        'order',
    ];

    protected $casts = [
        'is_main'      => 'boolean',
        'order' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Scopes & Helpers ─────────────────────────────────────

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get thumbnail version if your storage supports transformations
     * (Cloudinary, Imgix, Glide, etc.)
     */
    public function getThumbnailUrlAttribute(): string
    {
        // Example: Cloudinary auto-transformation
        // return str_replace('/upload/', '/upload/c_fill,w_400,h_300,q_auto/', $this->url);

        // Simple fallback – return original
        return $this->url;
    }
}
