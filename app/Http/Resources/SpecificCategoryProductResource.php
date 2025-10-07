<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificCategoryProductResource extends JsonResource {
    public function toArray($request) {
        return [
            'categoryId'      => $this->category_id,
            'categoryName'    => $this->category_name,
            'products' => $this->products->map(function ($product) {
                return [
                    'productId' => $product->product_id,
                    'productName' => $product->product_name,
                    'basePrice'       => $product->base_price,
                    'brandName'      => $product->brand->brand_name,
                    'typeName'      => $product->categorytype->type_name,
                    'imageThumbNail'         => optional(
                        $product->productVariant->first()?->mainImage
                    )?->url,
                ];
            }),
        ];
    }
}

