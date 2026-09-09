<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource 
{
    public function toArray(Request $request): array
    {
        // Detect if this is a variant product or a simple product
        $hasVariants = $this->productTypeVariant && $this->productTypeVariant->isNotEmpty();

        return [
            'productId'       => $this->product_id,
            'productTitle'    => $this->product_title,
            'basePrice'       => (float) $this->base_price,
            'description'     => $this->description,
            'features'        => $this->features,
            'specifications'  => $this->specifications,
            'isVariant'       => $hasVariants,

            // Simple Product Fields
            'sku'             => $this->sku,
            'stockQuantity'   => $this->stock_quantity,

            // Categorization
            'brand' => [
                'brandId'   => $this->brand_id,
                'brandName' => $this->brand?->brand_name,
            ],
            'category' => [
                'categoryId'   => $this->category_id,
                'categoryName' => $this->category?->category_name,
            ],
            'subCategory' => [
                'subCategoryId'   => $this->sub_category_id,
                'subCategoryName' => $this->subCategory?->sub_category_name, // Fixed relation name
            ],

            // General Product Media
            'media' => $this->images->map(function ($image) {
                return [
                    'imageId'  => $image->image_id,
                    'imageUrl' => $image->image_url,
                    'isMain'   => (bool) $image->isMain,
                ];
            }),

            // 1. Variant Attributes (Color, Size, etc.)
            'productVariants' => $this->whenLoaded('productTypeVariant', function () {
                return $this->productTypeVariant->map(function ($typeVariant) {
                    return [
                        'variantTypeId'   => $typeVariant->variant_type_id,
                        'variantTypeName' => $typeVariant->variant_name, // Fixed column name
                        'variantOptions'  => $typeVariant->variantOptions->map(function ($option) {
                            return [
                                'variantOptionId'    => $option->variant_option_id,
                                'variantOptionValue' => $option->variant_value, // Fixed column name
                                'imageUrl'           => $option->image?->image_url,
                            ];
                        }),
                    ];
                });
            }),

            // 2. Variant SKUs Matrix (Combinations with stock & price)
            'variantMatrix' => $this->whenLoaded('productSkus', function () {
                return $this->productSkus->map(function ($sku) {
                    return [
                        'skuId'            => $sku->sku_id,
                        'skuCode'          => $sku->sku_code,
                        'price'            => (float) $sku->price,
                        'stockQuantity'    => (int) $sku->stock_quantity,
                        'isActive'         => (bool) $sku->is_active,
                        'imageUrl'         => $sku->images->first()?->image_url,
                        // Array of Option IDs that make up this specific combination
                        'variantOptionIds' => $sku->variantOptions->pluck('variant_option_id')->toArray(),
                    ];
                });
            }),
        ];
    }
}