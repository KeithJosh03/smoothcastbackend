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
    'type_id',
    'product_name',
    'base_price',
    'description',
    'release'
    ];

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function productVariant(): HasMany {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    public function firstVariant(): HasOne {
        return $this->hasOne(ProductVariant::class, 'product_id')
                    ->orderBy('variant_id');
    }

    public function categorytype(): BelongsTo {
        return $this->belongsTo(CategoryType::class,'type_id');
    }

    public function specification(): HasOne {
        return $this->hasOne(Specification::class, 'product_id', 'product_id');
    }
    
    public function features(): HasOne {
        return $this->hasOne(Feature::class, 'product_id', 'product_id');
    }

}
