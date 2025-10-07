<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource {
    public function toArray($request): array {
        return [
            'packageId'   => $this->package_id,
            'variantId' => $this->variant_id,
            'setupId' => $this->setup_id,
        ];
    }
}


