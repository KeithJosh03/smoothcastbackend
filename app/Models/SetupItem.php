<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetupItem extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setup_item_id';

    protected $fillable = [
        'setup_id',
        'sku_id',
        'quantity'
    ];

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class, 'setup_id', 'setup_id');
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'sku_id', 'sku_id');
    }
}