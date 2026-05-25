<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListDashBoardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'productId' => $this->product_id,
            'productTitle' => $this->product_title,
            'basePrice' => $this->base_price,
            'brandName' => $this->brand->brand_name ?? null,
            'subCategoryName' => $this->subCategories->sub_category_name ?? null,
            'productTypeVariant' => ($this->productTypeVariant ?? collect())->map(function ($variant) {
            return [
                    'variantTypeName' => $variant->variant_type_name,
                    'variantOptions' => ($variant->variantOptions ?? collect())->map(function ($option) {
                return [
                            'optionId' => $option->variant_option_id,
                            'optionName' => $option->variant_option_value,
                            'priceAdjustment' => $option->price_adjustment,
                            'image' => optional($option->image)->image_url,
                        ];
            }
                ),
                ];
        }),
        ];
    }
}