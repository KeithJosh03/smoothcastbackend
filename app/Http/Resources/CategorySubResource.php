<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySubResource extends JsonResource {
    public function toArray($request) {
        return [
        'subCategoryId' => $this->sub_category_id,
        'subCategoryName' => $this->sub_category_name,
        ];
    }
}
