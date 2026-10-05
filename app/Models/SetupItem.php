<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetupItem extends Model
{
    public $timestamps = false;
    protected $table = 'setup_items';

    protected $fillable = [
        'setup_id',
        'product_id',
        'sku_id',
        'quantity',
        'is_required',
        'group_name'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class, 'setup_id', 'setup_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'sku_id', 'sku_id');
    }
}