<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificCategoryProductResource extends JsonResource {
    public function toArray($request) {
        $firstVariant = $this->productVariants->first();
        
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
            'productId' => $this->product_id,
            'productName' => $this->product_name,
            'basePrice'       => $this->base_price,
            'brandName'      => $this->brand->brand_name,
            'typeName'      => $this->categorytype->type_name,
            'imageThumbNail' => $imageUrl,
            'discountValue' => $discountedVariant?->discountsVariants?->first()?->discount_value ?? null,
            'discountType' => $discountedVariant?->discountsVariants?->first()?->discount_type ?? null
        ];
    }
}
