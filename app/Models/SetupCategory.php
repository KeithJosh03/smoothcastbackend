<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function setups()
    {
        return $this->hasMany(Setup::class, 'setup_category_id');
    }
}
