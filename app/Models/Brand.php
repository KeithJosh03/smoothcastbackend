<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model{

    public $timestamps = false;
    protected $primaryKey = 'brand_ID';
    protected $fillable = ['brand_name'];

    public function productsBranded():HasMany{
    
        return $this->hasMany(Product::class,'brand_ID');
    }
}
