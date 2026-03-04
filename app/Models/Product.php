<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'title',
        'slug',
        'description',
        'price',
        'price_negotiable',
        'currency',
        'condition',
        'year',
        'location',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'status',
        'is_featured',
        'is_urgent',
        'expires_at',
        'sold_to',
        'sold_at',
        'active_flag',
        'buyer_rating'
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'price_negotiable'  => 'boolean',
        'is_featured'       => 'boolean',
        'is_urgent'         => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'expires_at'        => 'datetime',
        'sold_at' => 'datetime',
        'specifications' => 'array',
    ];


    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->title) . '-' . uniqid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'PENDING')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeInSubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->ordered();
    }

    public function getMainThumbnailUrlAttribute(): ?string
    {
        return $this->mainImage
            ? asset('cloud/' . $this->mainImage->thumbnail_url)
            : null;
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->mainImage
            ? asset('cloud/' . $this->mainImage->url)
            : null;
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_main', true)
            ->orderBy('order', 'asc');
    }

    public function getPriceDisplayAttribute(): string
    {
        $formatted = number_format($this->price, 0);

        if (str_contains(strtolower($this->title), 'rent') || str_contains(strtolower($this->title), 'mo')) {
            return "₹{$formatted}/mo";
        }

        if ($this->price_negotiable) {
            return "₹{$formatted} (Negotiable)";
        }

        return "₹{$formatted}";
    }

    /**
     * Users who have favorited this product
     */
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps();  // keeps created_at / updated_at working
    }

    /**
     * Scope or helper: check if current user favorited this product
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * Optional: count of favorites (for performance)
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }

    /**
     * Simple check if the ad is marked as boosted/featured
     */
    public function isBoosted(): bool
    {
        return (bool) $this->is_featured;
    }

    /**
     * Temporary placeholder – returns fake boost info until you add proper tracking
     * Remove or replace this once you implement real boost purchases
     */
    public function activeBoost()
    {
        if (!$this->isBoosted()) {
            return null;
        }

        return (object) [
            'package_name' => 'Active Boost',
            'expires_at'   => null,
            'expires_in'   => 'Lifetime (temporary)',
        ];
    }


    public function isBoostedByCurrentUser(): bool
    {
        return (bool) $this->activeUserProductBoost();
    }


    public function userProductBoosts()
    {
        return $this->hasMany(UserProductBoost::class, 'product_id');
    }

    public function activeUserProductBoost()
    {
        return $this->userProductBoosts()
            ->where('expired_at', '>', now())
            ->with('boostPackage')
            ->latest('expired_at')
            ->first();
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
