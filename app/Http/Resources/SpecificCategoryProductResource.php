<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificCategoryProductResource extends JsonResource {

    public function toArray($request) {
        return [
            'productId'        => $this->product_id,
            'productTitle'     => $this->product_title,
            'basePrice'        => $this->base_price,
            'brandName'        => optional($this->brand)->brand_name,
            'subCategoryName'  => optional($this->subCategories)->sub_category_name,
            'productThumbNail' =>
                $this->productThumbNail->url
                ?? optional(
                    $this->productTypeVariantFirst?->variantOptionsFirstImage
                )->image_url,
        ];
    }
}

