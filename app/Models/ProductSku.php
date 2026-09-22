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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    /**
     * Appends custom dynamic attributes when serialized to array/JSON.
     */
    protected $appends = ['active_promotion', 'final_price'];

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

    /**
     * Accessor: Dynamically finds the active promotion applying to this SKU's product or category.
     */
    public function getActivePromotionAttribute()
    {
        $now = now();
        $productId = $this->product_id;
        $categoryId = $this->product?->category_id;
        $subCategoryId = $this->product?->sub_category_id;

        return Promotion::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function ($query) use ($productId, $categoryId, $subCategoryId) {
                // 1. Applies globally to ALL items
                $query->where('apply_to', 'ALL')
                // 2. Applies to specific PRODUCT
                ->orWhere(function ($q) use ($productId) {
                    $q->where('apply_to', 'PRODUCT')
                      ->whereHas('products', fn($p) => $p->where('products.product_id', $productId));
                })
                // 3. Applies to specific CATEGORY
                ->orWhere(function ($q) use ($categoryId, $subCategoryId) {
                    $q->where('apply_to', 'CATEGORY')
                      ->whereHas('categories', function ($c) use ($categoryId, $subCategoryId) {
                          $c->whereIn('categories.category_id', array_filter([$categoryId, $subCategoryId]));
                      });
                });
            })
            ->latest()
            ->first();
    }

    /**
     * Accessor: Calculates final price based on active promotion type (PERCENTAGE / FIXED_AMOUNT).
     */
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
}