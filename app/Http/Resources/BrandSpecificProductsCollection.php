<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BrandSpecificProductsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($product) {
            // 1. Intelligent Price Resolution (Prevents ₱0.00 display)
            $base = (float) $product->base_price;
            $min  = $product->min_variant_price !== null ? (float) $product->min_variant_price : 0;
            $max  = $product->max_variant_price !== null ? (float) $product->max_variant_price : 0;

            $minVal = ($base <= 0 && $min > 0) ? $min : ($min > 0 ? min($base, $min) : ($base > 0 ? $base : $min));
            $maxVal = ($base <= 0 && $max > 0) ? $max : ($max > 0 ? max($base, $max) : ($base > 0 ? $base : $max));

            $minVal = $minVal > 0 ? $minVal : $base;
            $maxVal = $maxVal > 0 ? $maxVal : $base;

            $displayPrice = $minVal === $maxVal 
                ? '₱' . number_format($minVal, 2)
                : '₱' . number_format($minVal, 2) . ' - ₱' . number_format($maxVal, 2);

            // 2. Active promotion logic
            $activePromo = $product->active_promotion ?? null;

            // 3. Complete Image Resolution Pipeline (Direct -> SKU -> Variant Option)
            $thumbnail = $product->mainImage?->image_url
                ?? $product->firstProductSku?->mainImage?->image_url
                ?? $product->productTypeVariantFirst?->firstVariantOption?->image?->image_url;

            return [
                'productId'       => $product->product_id,
                'productName'     => $product->product_title,
                'basePrice'       => $base,
                'minPrice'        => $minVal,
                'maxPrice'        => $maxVal,
                'formattedPrice'  => $displayPrice,
                'categoryType'    => $product->category?->category_name,
                'subCategoryName' => $product->subCategory?->sub_category_name,
                'mainImage'       => $thumbnail,
                'discount'        => $activePromo ? $activePromo->discount_type : null,
            ];
        });
    }
}