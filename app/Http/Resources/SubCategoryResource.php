<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource {
    public function toArray($request): array {
        return [
            'categoryId'   => $this->category_id,
            'subCategoryId' => $this->sub_category_id,
            'subCategoryName' => $this->sub_category_name
        ];
    }
}

