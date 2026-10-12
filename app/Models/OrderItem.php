<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'sku_id',
        'setup_id',
        'title_snapshot',
        'price_snapshot',
        'weight_snapshot',
        'quantity',
        'discount_applied_snapshot'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function sku()
    {
        return $this->belongsTo(ProductSku::class, 'sku_id', 'sku_id');
    }

    public function setup()
    {
        return $this->belongsTo(Setup::class, 'setup_id', 'setup_id');
    }
}
