<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model {
    public $timestamp = false;
    protected $primaryKey = 'variant_id';
    protected $fillable = [
        'product_id',
        'full_model_name',
        'price'
    ];

    
    public function product(): HasMany {
        return $this->hasMany(Product::class, 'product_id', 'product_id');
    }
}
