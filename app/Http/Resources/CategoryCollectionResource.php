<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollectionResource extends ResourceCollection {
    public function toArray($request) {
        return $this->collection->map(function ($category) {
            return [
                'categoryId'   => $category->category_id,
                'categoryName' => $category->category_name,
                'products'     => $category->products->map(fn($p) => [
                    'productId'   => $p->product_id,
                    'basePrice'   => $p->base_price,
                    'brandName'   => $p->brand->brand_name,
                    'productName' => $p->product_name,
                    'url'         => optional(
                        $p->productVariant->first()?->mainImage
                    )?->url,
                ]),
            ];
        });
    }
}
