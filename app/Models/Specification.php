<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Data\Eloquent\HasMany;

class Specification extends Model {
    public $timestamps = false;
    protected $primaryKey = 'specification_ID';
    protected $fillable = ['specificationName','variantspecification_ID'];

    public function variantspecification():HasMany {
        return $this->hasMany(VariantSpecification::class, 'variantspecification_ID');
    }

}
