<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'shipping_fee',
        'bulky_handling_fee',
        'discount_amount',
        'grand_total',
        'full_name',
        'phone_number',
        'street_address',
        'barangay',
        'city_municipality',
        'province',
        'postal_code',
        'region',
        'gcash_sender_number',
        'gcash_reference_number',
        'payment_status',
        'fulfillment_status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
