<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDiscountCollectionResource extends  JsonResource {
    public function toArray(Request $request): array {
        $mainImageUrl = optional($this->discountProductVariant->mainImage)->url;
        if (!$mainImageUrl) {
            $mainImageUrl = optional(
                $this->discountProductVariant->product
                    ->productVariants
                    ->firstWhere('mainImage', '!=', null)
            )?->mainImage?->url;
        }
        return [
            'discountId'     => $this->discount_id,
            'variantId'      => $this->variant_id,
            'endDate'        => $this->endDate,
            'productId'      => $this->discountProductVariant->product->product_id,
            'discountType'   => $this->discount_type,
            'discountValue'  => $this->discount_value,
            'productModel'   => $this->discountProductVariant->variant_name,
            'productPrice'   => $this->discountProductVariant->variant_price,
            'imageThumbNail' => $mainImageUrl,
            'brandName'      => $this->discountProductVariant->product->brand->brand_name,
            'productName'    => $this->discountProductVariant->product->product_name,
        ];
    }
}

