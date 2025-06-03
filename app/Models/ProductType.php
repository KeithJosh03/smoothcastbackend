<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $primaryKey = 'productType_ID';
    public $timestamp = false;
    protected $fillable = ['product_ID','product_type'];
}
