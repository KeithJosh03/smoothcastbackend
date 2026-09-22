<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecificCategoryProductResource extends JsonResource
{
    public function toArray($request)
    {
        // 1. Price Resolution
        $min = $this->min_variant_price;
        $max = $this->max_variant_price;
        $base = (float) $this->base_price;

        if ($min !== null) {
            $minVal = (float) $min;
            $maxVal = (float) $max;
            $displayPrice = $minVal === $maxVal 
                ? '₱' . number_format($minVal, 2)
                : '₱' . number_format($minVal, 2) . ' - ₱' . number_format($maxVal, 2);
        } else {
            $minVal = $base;
            $maxVal = $base;
            $displayPrice = '₱' . number_format($base, 2);
        }

        // 2. Image Resolution Pipeline
        $thumbnail = $this->mainImage?->image_url
            ?? $this->firstProductSku?->mainImage?->image_url
            ?? $this->productTypeVariantFirst?->firstVariantOption?->image?->image_url;

        return [
            'productId'        => $this->product_id,
            'productTitle'     => $this->product_title,
            'basePrice'        => $base,
            'minPrice'         => $minVal,
            'maxPrice'         => $maxVal,
            'formattedPrice'   => $displayPrice,
            'brandName'        => $this->brand?->brand_name,
            'subCategoryName'  => $this->subCategory?->sub_category_name,
            'productThumbNail' => $thumbnail,
        ];
    }
}