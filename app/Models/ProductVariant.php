<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProductVariant extends Model {
    public $timestamps = false;
    protected $primaryKey = 'variant_id';
    protected $fillable = [
    'product_id',
    'full_model_name',
    'product_price'
    ];
    
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function productimage(): HasMany {
        return $this->hasMany(ProductImage::class,'variant_id');
    }

    public function discounts(): HasMany {
        return $this->hasMany(ProductDiscount::class,'variant_id');
    }
    
    public function mainImage() {
        return $this->hasOne(ProductImage::class,'variant_id')
                ->where('isMain', 1);
    }

    public function allImage(): HasMany {
        return $this->hasMany(ProductImage::class,'variant_id');
    }

    public function package(): HasMany {
        return $this->hasMany(Package::class,'variant_id');
    }

}
