<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Blog extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'caption_html',
        'location',
        'likes_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function productTags(): HasMany
    {
        return $this->hasMany(BlogProductTag::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class);
    }
}