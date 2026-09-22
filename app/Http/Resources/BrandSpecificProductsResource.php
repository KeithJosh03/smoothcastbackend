<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandSpecificProducts extends JsonResource 
{
    public function toArray($request) 
    {
        $activePromo = $this->active_promotion;

        return [
            'productId'        => $this->product_id,
            'productName'      => $this->product_title,
            'basePrice'        => (float) $this->base_price,
            'minPrice'         => $this->getMinPrice(),
            'maxPrice'         => $this->getMaxPrice(),
            'formattedPrice'   => $this->getFormattedPrice(),
            'categoryType'     => $this->category?->category_name,
            'subCategoryName'  => $this->subCategory?->sub_category_name,
            'mainImage'        => $this->resolveThumbnail(),
            'discount'         => $activePromo ? $activePromo->discount_type : null,
        ];
    }

    protected function getMinPrice(): float
    {
        return $this->min_variant_price !== null 
            ? (float) $this->min_variant_price 
            : (float) $this->base_price;
    }

    protected function getMaxPrice(): float
    {
        return $this->max_variant_price !== null 
            ? (float) $this->max_variant_price 
            : (float) $this->base_price;
    }

    protected function getFormattedPrice(): string
    {
        $min = $this->min_variant_price;
        $max = $this->max_variant_price;

        if ($min !== null) {
            $minVal = (float) $min;
            $maxVal = (float) $max;

            return $minVal === $maxVal 
                ? '₱' . number_format($minVal, 2)
                : '₱' . number_format($minVal, 2) . ' - ₱' . number_format($maxVal, 2);
        }

        return '₱' . number_format((float) $this->base_price, 2);
    }

    protected function resolveThumbnail(): ?string
    {
        return $this->mainImage?->image_url
            ?? $this->firstProductSku?->mainImage?->image_url
            ?? $this->productTypeVariantFirst?->firstVariantOption?->image?->image_url;
    }
}