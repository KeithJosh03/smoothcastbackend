<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandSpecificProducts extends JsonResource {
    public function toArray($request) {
        $firstVariant = optional($this->productVariants)->first();
        
        // Find any variant that has discounts (check if discountsVariants collection has items)
        $discountedVariant = $this->productVariants->first(function($variant) {
            return $variant->discountsVariants && $variant->discountsVariants->isNotEmpty();
        });
        
        // Get image from first variant, or fallback to any variant with image
        $imageUrl = optional($firstVariant?->mainImage)?->url;
        if (!$imageUrl) {
            $imageUrl = optional(
                $this->productVariants->firstWhere('mainImage', '!=', null)
            )?->mainImage?->url;
        }
        
        return [
            'productId'    => $this->product_id,
            'productName'  => $this->product_name,
            'basePrice'    => $this->base_price,
            'categoryType' => $this->categorytype->type_name ?? null,
            'mainImage'    => $imageUrl,
            'discount'     => $discountedVariant?->discountsVariants?->first()?->discount_type ?? null,
        ];
    }

}
