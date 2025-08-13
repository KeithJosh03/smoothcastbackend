<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relation\BelongsTo;

class ProductVariant extends Model {
    public $timestamp = false;
    protected $primaryKey = 'variant_id';
    protected $fillable = [
        'product_id',
        'full_model_name',
        'price'
    ];

    
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
