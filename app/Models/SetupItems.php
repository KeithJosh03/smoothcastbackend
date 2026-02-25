<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetupItems extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setup_item_id';
    protected $fillable = [
        'setup_id',
        'product_id',
    ];


    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setups::class , 'setup_id');
    }

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class , 'setup_id');
    }


}