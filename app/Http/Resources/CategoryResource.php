<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {
    public function toArray($request): array {
        return [
            'categoryId'   => $this->category_id,
            'categoryName' => $this->category_name,
            'subcategoriesCount' => $this->sub_categories_count ?? 0,
        ];
    }
}

