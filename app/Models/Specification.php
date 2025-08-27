<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specification extends Model {
    public $timestamps = false;
    protected $primaryKey = 'specs_id';
    protected $fillable = [
    'product_id',
    'specification'
    ];

    public function products(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
