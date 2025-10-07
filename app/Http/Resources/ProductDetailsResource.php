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
            'specification'  => $this->specification->specification ?? null,
            'features'       => $this->features->features ?? null,
            'description'    => $this->description ?? null, 
            'typeName'       => $this->categorytype->type_name,
            'productVariant' => $this->productVariant->map(function ($variant) {
                return [
                    'productId' => $variant->product_id,
                    'variantId' => $variant->variant_id,
                    'name'       => $variant->full_model_name,
                    'price'      => $variant->product_price,
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
