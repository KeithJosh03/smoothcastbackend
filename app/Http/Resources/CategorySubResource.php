<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySubResource extends JsonResource {
    public function toArray($request) {
        return [
            'categoryId' => $this->category_id,
            'categoryName' => $this->category_name,
            'subCategories' => $this->subcategories->map(function ($subcat) {
                return [
                    'categoryId' => $subcat->category_id,
                    'subCategoryId' => $subcat->sub_category_id,
                    'subCategoryName' => $subcat->sub_category_name,
                ];
            }),
        ];
    }
}
