<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource {
    public function toArray($request): array {
        return [
            'brandId'   => $this->brand_id,
            'brandName' => $this->brand_name,
            'imageUrl'  => $this->when(
                !empty($this->image_url), 
                $this->image_url
            ),
        ];
    }
}

