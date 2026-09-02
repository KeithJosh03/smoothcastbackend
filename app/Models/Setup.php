<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setup extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setup_id';

    protected $fillable = [
        'setup_name',
        'setup_price',
        'available',
        'description'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SetupItem::class, 'setup_id', 'setup_id');
    }
}