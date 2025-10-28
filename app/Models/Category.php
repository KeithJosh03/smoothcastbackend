<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model{

    public $timestamps = false;
    protected $primaryKey = 'category_id';
    protected $fillable = [
    'category_name'
    ];

    public function products(): HasMany{
        return $this->hasMany(Product::class,'category_id');
    }

    public function subcategories(): HasMany {
        return $this->hasMany(SubCategory::class,'category_id');
    }
}
