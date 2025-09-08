<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Relations\BelongsTo;

class Feature extends Model {
    public $timestamps = false;
    protected $primaryKey = 'feature_id';
    protected $fillable = [
    'product_id',
    'features',
    ];

    public function product():BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
