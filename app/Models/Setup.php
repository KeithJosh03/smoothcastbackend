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

    public function promotions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Promotion::class,
            'promotion_setup',
            'setup_id',
            'promotion_id'
        );
    }

    public function getActivePromotionAttribute(): ?Promotion
    {
        $promotions = Promotion::currentlyActive()
            ->where(function ($query) {
                // 1. Applies store-wide
                $query->where('apply_to', 'ALL')
                // 2. Directly targets this specific setup
                ->orWhere(function ($q) {
                    $q->where('apply_to', 'SETUP')
                    ->whereHas('setups', fn($s) => $s->where('setups.setup_id', $this->setup_id));
                });
            })
            ->get();

        if ($promotions->isEmpty()) {
            return null;
        }

        $bestPromotion = null;
        $maxDiscount = -1;

        $price = (float) $this->bundle_price;

        foreach ($promotions as $promotion) {
            $discountAmount = 0;
            if ($promotion->discount_type === 'PERCENTAGE') {
                $discountAmount = $price * ($promotion->discount_value / 100);
            } else {
                $discountAmount = (float) $promotion->discount_value;
            }

            if ($discountAmount > $maxDiscount) {
                $maxDiscount = $discountAmount;
                $bestPromotion = $promotion;
            }
        }

        return $bestPromotion;
    }
}