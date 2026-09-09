<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySubResource extends JsonResource {
    public function toArray($request) {
        return [
            'categoryId' => $this->category_id,
            'subCategoryId' => $this->sub_category_id,
            'subCategoryName' => $this->sub_category_name,
            'isActive' => $this->is_active ?? true,
            'sortOrder' => $this->sort_order ?? 0,
        ];
    }
}
