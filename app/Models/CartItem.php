<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    
    protected $primaryKey = 'cart_item_id';
    
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'cart_id',
        'product_id',
        'sku_id',
        'setup_id',
        'quantity',
        'selections',
    ];

    protected $casts = [
        'selections' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function setup()
    {
        return $this->belongsTo(Setup::class, 'setup_id', 'setup_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function sku()
    {
        return $this->belongsTo(ProductSku::class, 'sku_id', 'sku_id');
    }
}