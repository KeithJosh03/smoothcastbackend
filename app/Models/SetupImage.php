<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetupImage extends Model {
    public $timestamps = false;
    protected $primaryKey = 'setup_img_id';
    protected $fillable = [
    'setup_id',
    'url',
    'isMain'
    ];

    public function setup(): BelongsTo {
        return $this->belongsTo(Setup::class,'setup_id');
    }
}
