<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model {

    public $timestamps = false;
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'category_name',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function products(): HasMany{
        return $this->hasMany(Product::class,'category_id');
    }

    public function subCategories(): HasMany {
        return $this->hasMany(SubCategory::class,'category_id');
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(
            Promotion::class,
            'promotion_category',
            'category_id',
            'promotion_id'
        );
    }
}
