<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Data\Eloquent\HasMany;

class Category extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'category_ID';
    protected $fillable = ['category_name'];

    public function product():HasMany
    {
        return $this->hasMany(Product::class,'category_ID');
    }
}
