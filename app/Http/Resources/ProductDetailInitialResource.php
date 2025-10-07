<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailInitialResource extends JsonResource {
   public function toArray($request) {
        return [
            'productId'      => $this->product_id,
            'productName'    => $this->product_name,
            'variantId'      => $this->firstVariant->variant_id
        ];
    }
}
