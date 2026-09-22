<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inclusion extends Model
{
    public $timestamps = false;
    protected $table = 'inclusions';

    protected $fillable = [
        'setup_id',
        'title',
        'price',
        'quantity',
        'is_required'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class, 'setup_id', 'setup_id');
    }
}
