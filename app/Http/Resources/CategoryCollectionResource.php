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
        return $this->collection
            // Filter out any categories whose active products list ended up empty
            ->filter(fn($category) => $category->products->isNotEmpty())
            ->map(function ($category) {
                return [
                    'categoryId' => $category->category_id,
                    'categoryName' => $category->category_name,
                    'products' => $category->products->map(function ($product) use ($category) {
                        $min = $product->min_variant_price;
                        $max = $product->max_variant_price;
                        $base = (float) $product->base_price;

                        $minVal = $min !== null ? (float) $min : $base;
                        $maxVal = $max !== null ? (float) $max : $base;

                        // Find matching promo for products
                        $applicablePromo = collect($this->promotions)->first(function ($promo) use ($product) {
                            if ($promo->apply_to === 'ALL') return true;
                            if ($promo->apply_to === 'PRODUCT') {
                                $productIds = $promo->products ? $promo->products->pluck('product_id')->toArray() : [];
                                return in_array($product->product_id, $productIds);
                            }
                            return false;
                        });

                        // Calculate discount if promo exists
                        $discountedMin = $minVal;
                        if ($applicablePromo) {
                            if ($applicablePromo->discount_type === 'PERCENTAGE') {
                                $discountedMin = max(0, round($minVal - ($minVal * ((float)$applicablePromo->discount_value / 100)), 2));
                            } else {
                                $discountedMin = max(0, round($minVal - (float)$applicablePromo->discount_value, 2));
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
                            'promotionName' => $applicablePromo?->name ?? null,
                            'productThumbNail' => $product->mainImage?->image_url ?? $product->firstProductSku?->mainImage?->image_url,
                            'productTitle' => $product->product_title,
                            'subCategoryName' => $product->subCategory?->sub_category_name,
                            'isActive' => (bool) ($product->is_active ?? true),
                            'is_active' => (bool) ($product->is_active ?? true),
                        ];
                    }),
                ];
            })
            ->values(); // Re-index array keys after filtering
    }
}