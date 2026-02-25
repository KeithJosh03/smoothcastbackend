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
    public $timestamps = false;
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'brand_id',
        'category_id',
        'sub_category_id',
        'product_title',
        'base_price',
        'description',
        'features',
        'specifications',
        'release_date'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class , 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class , 'category_id');
    }

    public function subCategories(): BelongsTo
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

    public function setupItems()
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


}