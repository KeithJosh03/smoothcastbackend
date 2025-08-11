<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specification extends Model {
    public $timestamps = false;
    protected $primaryKey = 'specs_id';
    protected $fillable = [
    'variant_id',
    'specs_name',
    'spec_value'
    ];

    public function variant(): BelongsTo {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
