<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandSpecificProducts extends JsonResource {
    public function toArray($request) {
        $firstVariant = $this->productVariant->first();

        return [
            'productId'    => $this->product_id,
            'productName'  => $this->product_name,
            'basePrice'    => $this->base_price,
            'categoryType' => $this->categorytype->type_name ?? null,
            'mainImage'    => optional($firstVariant?->mainImage)->url,
        ];
    }
}
