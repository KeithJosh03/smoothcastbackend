<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inclusion extends Model {
    protected $primaryKey = 'inclusion_id';
    public $timestamps = false;
    protected $fillable = [
    'setup_id',
    'inclusion'
    ];

    public function setup(): BelongsTo{
        return $this->belongsTo(Setup::class, 'setup_id');
    }
}
