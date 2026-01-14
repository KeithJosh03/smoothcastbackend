<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListDashBoardResource extends JsonResource {
    public function toArray($request){
        return [
            'productId' => $this->product_id,
            'productTitle' => $this->product_title,
            'basePrice' => $this->base_price,
            'brandName' => $this->brand->brand_name ?? null,
            'subCategoryName' => $this->subCategories->sub_category_name ?? null,

            // 'productImage' => $this->productThumbNail->url
            //     ?? optional(
            //         optional($this->productTypeVariant->first())
            //             ->variantOptionsFirstImage
            //             ->first()
            //     )->image_url,

            'productTypeVariant' => ($this->productTypeVariant ?? collect())->map(function ($variant) {
                return [
                    'variantTypeName' => $variant->variant_type_name,

                    'variantOptions' => ($variant->variantOptions ?? collect())->map(function ($option) {
                        return [
                            'optionName' => $option->variant_option_value,
                        ];
                    }),
                ];
            }),
        ];
    }
}
