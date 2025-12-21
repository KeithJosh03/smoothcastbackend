<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantOptions extends Model {
    public $timestamps = false;
    protected $primaryKey = 'variant_option_id';
    protected $fillable = [
    'variant_type_id',
    'variant_option_value',
    'price_adjustment',
    'image_url'
    ];

    public function variantType(): BelongsTo {
        return $this->belongsTo(ProductVariantType::class,'variant_type_id');
    }

}




