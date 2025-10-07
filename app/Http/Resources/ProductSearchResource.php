<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSearchResource extends JsonResource {
    public function toArray($request) {
        return [
            'productId'    => $this->product_id,
            'productName'  => $this->product_name
        ];
    }
}

