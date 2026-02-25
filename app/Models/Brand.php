<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Brand extends Model{

    public $timestamps = true;
    protected $primaryKey = 'brand_id';
    protected $fillable = [
    'brand_name',
    ];

    public function brandProducts():HasMany{
        return $this->hasMany(Product::class,'brand_id');
    }

    public function image(): MorphOne {
        return $this->morphOne(Image::class, 'imageable');
    }

}
