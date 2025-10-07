<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetupCollectionResource extends  JsonResource {
    public function toArray($request) {
        return [
            'setupId'      => $this->setup_id,
            'setupName'    => $this->setup_name,
            'valueDiscount'    => $this->value_discount,
            'codeName'    => $this->code_name,
            'typeDiscount' => $this->type_discount,
            'inclusion' => $this->inclusionSetup->inclusion ?? null,
            'setupImageThumbMail' => $this->mainImageSetup->url,
            'packages' => $this->package->map(function ($package) {
                return [
                    'variantId' => $package->variant_id,
                    'setupId'  => $package->setup_id,
                    'productName' => $package->packagevariant->product->product_name,
                    'categorytypeName' => $package->packagevariant->product->categorytype->type_name
                ];
            }),
        ];
    }
}
