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
        'setup_category_id',
        'description',
        'sku',
        'pricing_type',
        'retail_price',
        'bundle_price',
        'discount_percentage',
        'stock_quantity',
        'is_published',
        'start_date',
        'end_date',
        'weight',
        'is_bulky',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_bulky' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bundle_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    protected $appends = ['effective_weight', 'effective_is_bulky'];

    public function items(): HasMany
    {
        return $this->hasMany(SetupItem::class, 'setup_id', 'setup_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SetupCategory::class, 'setup_category_id');
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

    public function getEffectiveWeightAttribute(): int
    {
        if (!is_null($this->weight)) {
            return $this->weight;
        }
        
        return $this->items->sum(function ($item) {
            $qty = $item->quantity ?? 1;
            if ($item->sku) {
                return $item->sku->effective_weight * $qty;
            } elseif ($item->product) {
                return $item->product->weight * $qty;
            }
            return 0;
        });
    }

    public function getEffectiveIsBulkyAttribute(): bool
    {
        if (!is_null($this->is_bulky)) {
            return $this->is_bulky;
        }

        foreach ($this->items as $item) {
            if ($item->sku && $item->sku->effective_is_bulky) {
                return true;
            } elseif ($item->product && $item->product->is_bulky) {
                return true;
            }
        }
        return false;
    }
}