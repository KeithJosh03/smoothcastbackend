<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetupDetailsResource extends JsonResource {
    public function toArray($request) {
        $totalProductPrice = $this->package->sum(function ($package) {
            return (float)($package->packagevariant->product_price ?? 0);
        });

        return [
            'setupId'      => $this->setup_id,
            'setupName'    => $this->setup_name,
            'codeName'    => $this->code_name,
            'description'    => $this->description,
            'endDate'    => $this->end_date,
            'startDate'    => $this->start_date,
            'valueDiscount' => $this->value_discount,
            'typeDiscount' => $this->type_discount,
            'totalSetupPrice' => $totalProductPrice,
            'package' => $this->package->map(function ($p) {
                return [
                    'variantId' => $p->variant_id,
                    'productId' => $p->packagevariant->product_id,
                    'productName' => $p->packagevariant->full_model_name,
                    'categoryType' => $p->packagevariant->product->categorytype->type_name
                ];
            }),
            'images' => $this->setupimages->map(function ($img) {
                return [
                    'url' => $img->url,
                    'isMain' => $img->isMain,
                    'imageId' => $img->setup_img_id
                ];
            })
        ];
    }
}




