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

    public function variantOptionsFirstImage(): HasOne {
        return $this->hasOne(
            VariantOptions::class,
            'variant_type_id',  
            'variant_type_id'       
        )->orderBy('variant_option_id');
    }

    public function variantOptionsFirst(): HasOne {
        return $this->hasOne(VariantOptions::class, 'variant_type_id')
                    ->first()
                    ->limit(1);
    }
    

}


