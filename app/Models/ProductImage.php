<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProductImage extends Model {
    public $timestamps = false;
    protected $primaryKey = 'productImg_id';
    protected $fillable = [
    'variant_id',
    'url'
    ];


    public function productvariants(): HasMany {
        return $this->hasMany(ProductVariant::class, 'variant_id', 'variant_id');
    }
}
