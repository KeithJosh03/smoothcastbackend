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

    public function setups(): BelongsToMany
    {
        return $this->belongsToMany(
            Setup::class,
            'promotion_setup',
            'promotion_id',
            'setup_id'
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

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where(function ($q) use ($now) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                     });
    }

    public function calculateDiscount(float $price): float
    {
        if ($this->discount_type === 'PERCENTAGE') {
            return $price * ($this->discount_value / 100);
        }
        
        return min($price, (float) $this->discount_value);
    }
}