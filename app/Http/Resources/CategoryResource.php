<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {
    public function toArray($request): array {
        return [
            'categoryId'   => $this->category_id ?? $this->id,
            'categoryName' => $this->category_name ?? $this->name,
            'subcategoriesCount' => $this->sub_categories_count ?? 0,
            'isActive' => $this->is_active ?? true,
            'sortOrder' => $this->sort_order ?? 0,
        ];
    }
}

