<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setups extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setup_id';

    protected $fillable = [
        'setup_name',
        'setup_price',
        'available',
        'description'
    ];

    public function setupItems(): HasMany
    {
        return $this->hasMany(SetupItems::class , 'setup_id', 'setup_id');
    }

    public function setupItemVariants(): HasMany
    {
        return $this->hasMany(SetupItemVariant::class , 'setup_id', 'setup_id');
    }
}