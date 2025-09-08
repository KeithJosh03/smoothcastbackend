<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductImage extends Model {
    public $timestamps = false;
    protected $primaryKey = 'productImg_id';
    protected $fillable = [
    'variant_id',
    'url'
    ];

    public function productvariants(): BelongsTo {
        return $this->belongsTo(ProductVariant::class,'variant_id');
    }
}
