<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SetupResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'setupId'   => $this->setup_id,
            'setupName' => $this->setup_name,
            'codeName'  => $this->code_name,
            'allPrice'  => $this->all_price,
            'description' => $this->description
        ];
    }
}
