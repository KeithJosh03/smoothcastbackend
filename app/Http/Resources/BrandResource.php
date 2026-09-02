<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'brandId' => $this->brand_id,
            'brandName' => $this->brand_name,
            'linkedProducts' => $this->brand_products_count ?? 0,
            'imageUrl' => $this->whenLoaded('image', function () {
            return $this->image->image_url ?? null;
        }),
        ];
    }
}