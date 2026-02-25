<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model {
    protected $primaryKey = 'image_id'; 
    protected $fillable = [
        'image_url',
        'isMain',
    ];

    public function imageable(): MorphTo {
        return $this->morphTo();
    }
}