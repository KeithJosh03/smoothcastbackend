<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Setup extends Model {
    public $timestamps = false;
    protected $primaryKey = 'setup_id';
    protected $fillable = [
    'setup_name',
    'code_name',
    'description',
    'start_date',
    'end_date',
    'value_discount',
    'type_discount'
    ];

    public function package(): HasMany {
        return $this->hasMany(Package::class,'setup_id');
    }

    public function setupimages(): HasMany {
        return $this->hasMany(SetupImage::class,'setup_id');
    }

    public function mainImageSetup() {
        return $this->hasOne(SetupImage::class,'setup_id')
                ->where('isMain', 1);
    }

}
