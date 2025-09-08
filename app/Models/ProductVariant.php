<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProductVariant extends Model {
    public $timestamp = false;
    protected $primaryKey = 'variant_id';
    protected $fillable = [
        'product_id',
        'name',
        'price'
    ];

    
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productimage(): HasMany {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    
    public function mainImage() {
        return $this->hasOne(ProductImage::class, 'variant_id', 'variant_id')
                ->where('isMain', 1);
    }

    public function allImage() {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }
}
