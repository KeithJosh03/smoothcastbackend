<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Product extends Model {

    protected $primaryKey = 'product_id';
    protected $fillable = [
    'brand_id',
    'category_id',
    'sub_category_id',
    'product_name',
    'base_price',
    'description',
    'features',
    'specifications',
    'release'
    ];

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function productVariants(): HasMany {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    public function firstVariant(): HasOne {
        return $this->hasOne(ProductVariant::class, 'product_id')
                    ->orderBy('variant_id');
    }

    public function subcategory(): BelongsTo {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function scopeLatestArrivals($query) {
        return $query->orderBy('release', 'desc')->limit(8);
    }
}
