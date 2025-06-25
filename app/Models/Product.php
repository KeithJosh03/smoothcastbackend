<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'product_ID';
    protected $fillable = [
    'brand_ID',
    'category_ID',
    'productName',
    'price',
    'description'
    ];

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class, 'brand_ID');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_ID');
    }
}
