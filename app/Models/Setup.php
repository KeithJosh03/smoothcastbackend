<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\Image;

class Setup extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'setup_id';

    protected $fillable = [
        'bundle_title',
        'slug',
        'description',
        'sku',
        'pricing_type',
        'bundle_price',
        'discount_percentage',
        'stock_quantity',
        'is_published',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bundle_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SetupItem::class, 'setup_id', 'setup_id');
    }

    public function inclusions(): HasMany
    {
        return $this->hasMany(Inclusion::class, 'setup_id', 'setup_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
            ->where('isMain', true);
    }
}