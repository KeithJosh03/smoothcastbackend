<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource {
    public function toArray($request) {
        return [
            'productId'      => $this->product_id,
            'productTitle'   => $this->product_title,
            'basePrice'      => $this->base_price,
            'specifications'  => $this->specifications ?? null,
            'features'       => $this->features ?? null,
            'description'    => $this->description ?? null, 
            'subCategory'    => [
                'subCategoryName' => $this->subCategories->sub_category_name,
                'subCategoryId' => $this->subCategories->sub_category_id
            ],
            'category'    => [
                'categoryName' => $this->category->category_name,
                'categoryId' => $this->category->category_id
            ],
            'brand'      => [
                'brandName' => $this->brand->brand_name, 
                'brandId' => $this->brand->brand_id
                ?? null
            ],
            'productMedias' => $this->productMediaImage->isEmpty() ? null : 
                                $this->productMediaImage->map(function ($media) {
                                    return [
                                    'productImgId' => $media->product_img_id,
                                    'imageUrl' => $media->url,
                                    'isMain' => $media->isMain
                                    ];
                                }), 
            'productVariantsTypes' => $this->productTypeVariant->isEmpty() ? null :
                        $this->productTypeVariant->map(function ($typeVariant) {
                            return [
                            'variantTypeId' => $typeVariant->variant_type_id,
                            'variantTypeName' => $typeVariant->variant_type_name,
                            'variantOptions' => $typeVariant->variantOptions->map(function ($option) {
                                return [
                                'variantOptionId' => $option->variant_option_id,
                                'imageUrl' => $option->image_url,
                                'variantOptionValue' => $option->variant_option_value,
                                'variantOptionPrice' => $option->price_adjustment
                                ];
                            })
                            ];
                        })
        ];
    }

}
