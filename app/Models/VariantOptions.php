<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VariantOptions extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'variant_option_id';
    protected $fillable = [
        'variant_type_id',
        'variant_option_value',
        'price_adjustment',
    ];


    public function setupItemVariant(): HasMany
    {
        return $this->hasMany(SetupItemVariant::class , 'variant_option_id', 'variant_option_id');
    }

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(ProductVariantType::class , 'variant_type_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class , 'imageable');
    }
}