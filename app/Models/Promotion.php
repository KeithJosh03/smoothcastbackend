<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Promotion extends Model
{
    protected $primaryKey = 'promotion_id';

    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'apply_to',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // --- Relationships ---

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'promotion_category',
            'promotion_id',
            'category_id'
        );
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'promotion_product',
            'promotion_id',
            'product_id'
        );
    }

    // --- Query Scopes ---

    /**
     * Fetch only campaigns that are enabled and currently running
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }

    // --- Price Calculation Helper ---

    /**
     * Calculate discount amount given a price
     */
    public function calculateDiscount(float $price): float
    {
        if ($this->discount_type === 'PERCENTAGE') {
            return $price * ($this->discount_value / 100);
        }
        
        return min($price, (float) $this->discount_value);
    }
}