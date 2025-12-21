<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollectionResource extends ResourceCollection {
    public function toArray($request) {
        return $this->collection->map(function ($category) {
            return [
            'categoryId' => $category->category_id,
            'categoryName' => $category->category_name,
            'products' => $category->products->map(function ($product) {
                return [
                'productId' => $product->product_id,
                'basePrice' => $product->base_price,
                'brandName' => $product->brand->brand_name,
                'productThumbNail' =>
                    $product->productThumbNail->url
                    ?? optional(
                        optional($product->productTypeVariant->first())
                            ->variantOptionsFirstImage
                            ->first()
                    )->image_url,
                'productTitle' => $product->product_title,
                'subCategoryName' => $product->subCategories->sub_category_name,
                ];
            }),
            ];
        });
    }
}
