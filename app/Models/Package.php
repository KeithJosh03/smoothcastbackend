<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Package extends Model {
    protected $primaryKey = 'package_id';
    public $timestamps = false;
    protected $fillable = [
    'variant_id',
    'setup_id'
    ];

    public function packagevariant(): BelongsTo {
        return $this->belongsTo(ProductVariant::class,'variant_id');
    }

    public function setup(): BelongsTo {
        return $this->belongsTo(Setup::class,'setup_id');
    }
}
