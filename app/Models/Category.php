<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'category',
        'sub_category',
        'parent_id',
        'active_flag',
        'category_icon',
        // 'created_at', 'updated_at' → managed automatically
    ];

    protected $casts = [
        'active_flag'   => 'boolean',     // or 'integer' if you prefer 0/1
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'parent_id'     => 'integer',
    ];

    protected $appends = [
        'icon_url',           // ← automatically includes icon_url in toArray()/toJson()
    ];

    /**
     * Parent category (if this is a sub-category)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    /**
     * All direct child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    /**
     * Only active categories (scope)
     */
    public function scopeActive($query)
    {
        return $query->where('active_flag', 1);
    }

    /**
     * Convenience method: is this a top-level category?
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id')
            ->orWhere('subcategory_id', $this->id);
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->active()
            ->orderBy('created_at', 'asc');
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->category_icon
            ? url('cloud/' . $this->category_icon)
            : null;
    }
}
