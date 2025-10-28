<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollectionResource extends ResourceCollection {
    public function toArray($request) {
        return $this->collection->map(function ($category) {
            return [
                'categoryId'   => $category->category_id,
                'categoryName' => $category->category_name,
                'products'     => $category->products->map(function($p) {

                    $firstVariant = $p->productVariants->first();
                    
                    $discountedVariant = $p->productVariants->first(function($variant) {
                        return $variant->discountsVariants && $variant->discountsVariants->isNotEmpty();
                    });
                    
                    $imageUrl = optional($firstVariant?->mainImage)?->url;
                    if (!$imageUrl) {
                        $imageUrl = optional(
                            $p->productVariants->firstWhere('mainImage', '!=', null)
                        )?->mainImage?->url;
                    }
                    
                    return [
                        'productId'   => $p->product_id,
                        'basePrice'   => $p->base_price,
                        'categoryType' => optional($firstVariant?->product?->categorytype)?->type_name,
                        'brandName'   => $p->brand->brand_name,
                        'productName' => $p->product_name,
                        'imageThumbNail' => $imageUrl,
                        'discountType'    => $discountedVariant?->discountsVariants?->first()?->discount_type ?? null
                    ];
                }),
            ];
        });
    }
}
