<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogProductTag extends Model
{
    protected $fillable = ['blog_id', 'product_id'];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
