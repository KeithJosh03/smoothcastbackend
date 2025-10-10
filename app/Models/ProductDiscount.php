<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductDiscount extends Model {
    protected $primaryKey = 'discount_id';
    protected $fillable = [
    'variant_id',
    'startDate',
    'endDate',
    'discount_type',
    'discount_value'
    ];
    

    public function discountProductVariant(): BelongsTo {
        return $this->belongsTo(ProductVariant::class,'variant_id');
    }

    public function scopeValid($query) {
        $now = Carbon::now();
        return $query->where('startDate', '<=', $now)
                    ->where('endDate', '>=', $now);
    }

}
