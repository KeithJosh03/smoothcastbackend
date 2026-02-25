<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetupItemVariant extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setup_item_variant';
    protected $fillable = [
        'setup_id',
        'variant_option_id',
    ];

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setups::class , 'setup_id');
    }

    public function variantOptions(): BelongsTo
    {
        return $this->belongsTo(VariantOptions::class , 'variant_option_id');
    }
}