<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model{

    public $timestamps = false;
    protected $primaryKey = 'brand_id';
    protected $fillable = [
    'brand_name',
    'urlImage'
    ];

    public function brandProducts():HasMany{
        return $this->hasMany(Product::class,'brand_id');
    }
}
