<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BoostAdd extends Model
{
    protected $table = 'boost_adds';  // important - table name is not the default plural

    protected $primaryKey = 'id';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'price',
        'icon',
        'slug',
        'duration_days',
        'view_multiplier',
        'features',
        'badge_text',
        'active_flag',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price'           => 'decimal:2',
        'duration_days'   => 'integer',
        'view_multiplier' => 'integer',
        'features'        => 'array',           // automatically handles json ↔ array
        'active_flag'     => 'boolean',         // 1/0 → true/false
        'sort_order'      => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // ────────────────────────────────────────────────
    // Accessors / Mutators (optional but very useful)
    // ────────────────────────────────────────────────

    /**
     * Get formatted price with ₹ symbol
     */
    protected function priceDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => '₹' . number_format($this->price, 0),
        );
    }

    /**
     * Check if this package is marked as popular/highlighted
     */
    public function isPopular(): bool
    {
        return $this->badge_text === 'POPULAR';
    }

    /**
     * Scope: only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('active_flag', 1);
    }

    /**
     * Scope: ordered by sort_order (lowest number = highest priority)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}