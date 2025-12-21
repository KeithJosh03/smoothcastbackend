<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource {
    public function toArray($request) {
        return [
            'productId'      => $this->product_id,
            'productName'    => $this->product_name,
            'basePrice'      => $this->base_price,
            'brandName'      => $this->brand->brand_name ?? null,
            'specification'  => $this->specifications ?? null,
            'features'       => $this->features ?? null,
            'description'    => $this->description ?? null, 
            'subCategoryName'       => $this->subcategory->sub_category_name,
            'productVariant' => $this->productVariants->map(function ($variant) {
                $discount = $variant->discountsVariants->first();
                return [
                    'productId' => $variant->product_id,
                    'variantId' => $variant->variant_id,
                    'variantName'       => $variant->variant_name,
                    'price'      => $variant->variant_price,
                    'discountPrice' => $discount ? $discount->discount_value : null,
                    'discountType' => $discount ? $discount->discount_type : null,   
                    'discountEnd' => $discount ? $discount->endDate : null,          
                    'allImage'  => $variant->allImage->map(function ($img) {
                        return [
                            'productimageId' => $img->product_img_id,
                            'variantId'    => $img->variant_id,
                            'url'           => $img->url,
                            'isMain'        => $img->isMain,
                        ];
                    }),
                ];
            }),
        ];
    }

}
