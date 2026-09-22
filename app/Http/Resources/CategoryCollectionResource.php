<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollectionResource extends ResourceCollection {
    protected $promotions;

    public function additionalPromotions($promotions) {
        $this->promotions = $promotions;
        return $this;
    }

    public function toArray($request) {
        return $this->collection->map(function ($category) {
            return [
                'categoryId' => $category->category_id,
                'categoryName' => $category->category_name,
                'products' => $category->products->map(function ($product) use ($category) {
                    $min = $product->min_variant_price;
                    $max = $product->max_variant_price;
                    $base = (float) $product->base_price;

                    $minVal = $min !== null ? (float) $min : $base;
                    $maxVal = $max !== null ? (float) $max : $base;

                    // Find matching promo
                    $applicablePromo = collect($this->promotions)->first(function ($promo) use ($category, $product) {
                        if ($promo->apply_to === 'ALL') return true;
                        if ($promo->apply_to === 'CATEGORY' && in_array($category->category_id, $promo->target_ids ?? [])) return true;
                        if ($promo->apply_to === 'PRODUCT' && in_array($product->product_id, $promo->target_ids ?? [])) return true;
                        return false;
                    });

                    // Calculate discount if promo exists
                    $discountedMin = $minVal;
                    if ($applicablePromo) {
                        if ($applicablePromo->discount_type === 'PERCENTAGE') {
                            $discountedMin = $minVal - ($minVal * ($applicablePromo->discount_value / 100));
                        } else {
                            $discountedMin = max(0, $minVal - $applicablePromo->discount_value);
                        }
                    }

                    return [
                        'productId' => $product->product_id,
                        'categoryId' => $category->category_id,
                        'basePrice' => $base,
                        'minPrice' => $minVal,
                        'maxPrice' => $maxVal,
                        'discountedMinPrice' => $discountedMin,
                        'hasDiscount' => $discountedMin < $minVal,
                        'discountLabel' => $applicablePromo 
                            ? ($applicablePromo->discount_type === 'PERCENTAGE' 
                                ? "-{$applicablePromo->discount_value}%" 
                                : "-₱{$applicablePromo->discount_value}")
                            : null,
                        'productThumbNail' => $product->mainImage?->image_url ?? $product->firstProductSku?->mainImage?->image_url,
                        'productTitle' => $product->product_title,
                        'subCategoryName' => $product->subCategory?->sub_category_name,
                    ];
                }),
            ];
        });
    }
}