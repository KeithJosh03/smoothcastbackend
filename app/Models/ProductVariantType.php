<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;



class ProductVariantType extends Model {
    public $timestamps = false;
    protected $primaryKey = 'variant_type_id';
    protected $fillable = [
    'product_id',
    'variant_type_name',
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function variantOptions(): HasMany {
        return $this->hasMany(VariantOptions::class,'variant_type_id');
    }

    public function variantOptionsFirstImage(): HasMany {
        return $this->hasMany(VariantOptions::class, 'variant_type_id')
                    ->limit(1);
    }
    
    // public function discountsVariants(): HasMany {
    //     return $this->hasMany(ProductDiscount::class,'variant_id')->valid();
    // }
    
    // public function mainImage() {
    //     return $this->hasOne(ProductImage::class,'variant_id')
    //             ->where('isMain', 1);
    // }

    // public function allImage(): HasMany {
    //     return $this->hasMany(ProductImage::class,'variant_id');
    // }

    // public function package(): HasMany {
    //     return $this->hasMany(Package::class,'variant_id');
    // }
}


