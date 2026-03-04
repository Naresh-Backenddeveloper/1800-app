<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductBoost extends Model
{
    protected $table = 'user_product_boosts';

    protected $fillable = [
        'user_id',
        'product_id',
        'boost_add_id',
        'price',
        'expired_at',
        'slug'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function boostPackage()
    {
        return $this->belongsTo(BoostAdd::class, 'boost_add_id');
    }

    public function isActive()
    {
        return $this->expired_at && $this->expired_at->isFuture();
    }


    public function getSortPriorityAttribute()
    {
        $slug = $this->boostPackage?->slug ?? '';

        return match (strtolower($slug)) {
            'premium', 'premuim' => 30,
            'standard', 'standerd' => 20,
            'basic' => 10,
            default => 0,
        };
    }
}
