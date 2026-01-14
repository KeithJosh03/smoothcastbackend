<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource {
    public function toArray($request) {
        return [
            'productId'      => $this->product_id,
            'productTitle'    => $this->product_title,
            'basePrice'      => $this->base_price,
            'brandName'      => $this->brand->brand_name ?? null,
            'specification'  => $this->specifications ?? null,
            'features'       => $this->features ?? null,
            'description'    => $this->description ?? null, 
            'subCategoryName'       => $this->subCategories->sub_category_name,
            'productMedias' => $this->productMediaImage->isEmpty() ? null : 
                                $this->productMediaImage->map(function ($media) {
                                    return [
                                    'imageUrl' => $media->url,
                                    'isMain' => $media->isMain
                                    ];
                                }), 
            'productVariants' => $this->productTypeVariant->isEmpty() ? null :
                        $this->productTypeVariant->map(function ($typeVariant) {
                            return [
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
