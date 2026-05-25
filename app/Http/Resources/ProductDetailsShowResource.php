<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsShowResource extends JsonResource{
     public function toArray($request)
    {
        return [
            'productId'    => $this->product_id,
            'productTitle' => $this->product_title,
            'basePrice'    => $this->base_price,

            'specifications' => $this->specifications,
            'features'       => $this->features,
            'description'    => $this->description,
            'brandName' => $this->brand?->brand_name,
            'subCategoryName' => $this->subCategories?->sub_category_name,
            'productMedias' => $this->images->isEmpty() ? null :
                $this->images->map(function ($image) {
                    return [
                        'imageUrl' => $image->image_url,
                        'isMain'   => (bool) $image->isMain,
                        'imageId'  => $image->image_id,
                    ];
            }),

            'productVariants' => $this->whenLoaded('productTypeVariant', function () {
                return $this->productTypeVariant->map(function ($typeVariant) {
                    return [
                        'variantTypeId'   => $typeVariant->variant_type_id,
                        'variantTypeName' => $typeVariant->variant_type_name,
                        'variantOptions'  => $typeVariant->variantOptions->map(function ($option) {
                            return [
                                'variantOptionId'    => $option->variant_option_id,
                                'imageUrl'           => $option->image?->image_url,
                                'variantOptionValue' => $option->variant_option_value,
                                'variantOptionPrice' => $option->price_adjustment,
                            ];
                        }),
                    ];
                });
            }),
        ];
    }

}