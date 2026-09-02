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
        'price' => 'decimal:2',
    ];

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
}
