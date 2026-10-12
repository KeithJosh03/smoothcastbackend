<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ProductSku extends Model
{
    protected $primaryKey = 'sku_id';
    
    protected $fillable = [
        'product_id',
        'sku_code',
        'price',
        'stock_quantity',
        'is_active',
        'packaging_type',
        'weight_g',
        'weight',
        'is_bulky',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_bulky'  => 'boolean',
        'weight_g'  => 'integer',
        'price'     => 'decimal:2',
    ];

    protected $appends = ['active_promotion', 'final_price', 'effective_weight', 'effective_is_bulky', 'effective_packaging_type'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantOptions(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantOptions::class,
            'sku_variant_option',
            'sku_id',
            'variant_option_id'
        );
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
            ->where('isMain', true);
    }

    public function getActivePromotionAttribute()
    {
        $productId = $this->product_id;

        return Promotion::currentlyActive()
            ->where(function ($query) use ($productId) {
                $query->where('apply_to', 'ALL')
                ->orWhere(function ($q) use ($productId) {
                    $q->where('apply_to', 'PRODUCT')
                      ->whereHas('products', fn($p) => $p->where('products.product_id', $productId));
                });
            })
            ->latest()
            ->first();
    }


    public function getFinalPriceAttribute(): float
    {
        $originalPrice = (float) $this->price;
        $promotion = $this->active_promotion;

        if (!$promotion) {
            return $originalPrice;
        }

        if ($promotion->discount_type === 'PERCENTAGE') {
            $discount = ($originalPrice * (float) $promotion->discount_value) / 100;
            return max(0, round($originalPrice - $discount, 2));
        }

        if ($promotion->discount_type === 'FIXED_AMOUNT') {
            return max(0, round($originalPrice - (float) $promotion->discount_value, 2));
        }

        return $originalPrice;
    }

    public function getEffectiveWeightAttribute(): int
    {
        return $this->weight_g ?? $this->weight ?? $this->product->weight_g ?? $this->product->weight ?? 0;
    }

    public function getEffectiveIsBulkyAttribute(): bool
    {
        if (in_array($this->packaging_type, ['BOX_L', 'LONG_PARCEL'])) {
            return true;
        }
        if ($this->is_bulky !== null) {
            return (bool) $this->is_bulky;
        }
        if ($this->product && in_array($this->product->packaging_type, ['BOX_L', 'LONG_PARCEL'])) {
            return true;
        }
        return (bool) ($this->product->is_bulky ?? false);
    }

    public function getEffectivePackagingTypeAttribute(): string
    {
        return $this->packaging_type ?? $this->product->packaging_type ?? 'POUCH_S';
    }
}