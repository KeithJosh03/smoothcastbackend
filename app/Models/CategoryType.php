<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryType extends Model {

    protected $timestamp = false;
    protected $primaryKey = 'type_id';
    protected $fillable = [
    'type_name',
    'category_id'
    ];

    public function category():HasMany {
        return $this->hasMany(Category::class, 'category_id');
    }

    public function getRouteKeyName() {
        return 'type_id';
    }
}
