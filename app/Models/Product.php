<?php

namespace App\Models;

use App\Models\Image;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class Product extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'brand_id', 'category_id', 'sub_category_id', 
        'product_title', 'base_price', 'description', 
        'features', 'specifications', 'release_date',
        'sku', 'stock_quantity', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class , 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class , 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class , 'sub_category_id');
    }

    public function productTypeVariant(): HasMany
    {
        return $this->hasMany(ProductVariantType::class , 'product_id', 'product_id');
    }

    public function productTypeVariantFirst()
    {
        return $this->hasOne(ProductVariantType::class , 'product_id', 'product_id')
            ->orderBy('variant_type_id');
    }

    public function productSkus(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'product_id', 'product_id');
    }

    public function firstProductSku(): HasOne
    {
        return $this->hasOne(ProductSku::class, 'product_id', 'product_id')->orderBy('sku_id', 'asc');
    }

    public function setupItems(): HasMany
    {
        return $this->hasMany(SetupItems::class , 'product_id', 'product_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class , 'imageable');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class , 'imageable')
            ->where('isMain', true);
    }

    public function scopeLatestArrivals($query)
    {
        return $query->orderBy('release_date', 'desc')->limit(8);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(
            Promotion::class,
            'promotion_product',
            'product_id',
            'promotion_id'
        );
    }

    public function getActivePromotionAttribute(): ?Promotion
    {
        $promotions = Promotion::currentlyActive()
            ->where(function ($query) {
                $query->where('apply_to', 'ALL')
                ->orWhere(function ($q) {
                    $q->where('apply_to', 'PRODUCT')
                    ->whereHas('products', fn($p) => $p->where('products.product_id', $this->product_id));
                });
            })
            ->get();

        if ($promotions->isEmpty()) {
            return null;
        }

        $bestPromotion = null;
        $maxDiscount = -1;

        $price = (float) $this->base_price;

        foreach ($promotions as $promotion) {
            $discountAmount = 0;
            if ($promotion->discount_type === 'PERCENTAGE') {
                $discountAmount = $price * ($promotion->discount_value / 100);
            } else {
                $discountAmount = (float) $promotion->discount_value;
            }

            if ($discountAmount > $maxDiscount) {
                $maxDiscount = $discountAmount;
                $bestPromotion = $promotion;
            }
        }

        return $bestPromotion;
    }


}