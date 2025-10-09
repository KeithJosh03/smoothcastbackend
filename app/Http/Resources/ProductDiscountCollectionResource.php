<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDiscountCollectionResource extends  JsonResource {
    public function toArray(Request $request): array {
        return [
            'discountId'   => $this->discount_id,
            'variantId' => $this->variant_id,
            'productId' => $this->discountProductVariant->product->product_id,
            'discountType'  => $this->discount_type,
            'discountValue'  => $this->discount_value,
            'productModel' => $this->discountProductVariant->full_model_name,
            'productPrice' => $this->discountProductVariant->product_price,
            'imageThumbNail' => $this->discountProductVariant->mainImage->url,
            'brandName' => $this->discountProductVariant->product->brand->brand_name
        ];
    }
}

