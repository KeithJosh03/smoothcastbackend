<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductMediaImage extends Model {
    public $timestamps = false;
    protected $primaryKey = 'product_img_id';
    protected $fillable = [
    'product_id',
    'url',
    'isMain'
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(ProductVariant::class,'product_id');
    }
}
