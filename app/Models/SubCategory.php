<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends Model {

    public $timestamps = false;
    protected $primaryKey = 'sub_category_id';
    protected $fillable = [
    'sub_category_name',
    'category_id'
    ];

    public function category():BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function getRouteKeyName() {
        return 'sub_category_id';
    }
}
