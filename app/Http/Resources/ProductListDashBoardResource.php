<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListDashBoardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'productId' => $this->product_id,
            'productTitle' => $this->product_title,
            'basePrice' => $this->base_price,
            'brandName' => $this->brand->brand_name ?? null,
            'categoryName' => $this->category->category_name ?? null,
            'subCategoryName' => $this->subCategory->sub_category_name ?? null,
            'productTypeVariant' => ($this->productTypeVariant ?? collect())->map(function ($variant) {
                return [
                    'variantTypeName' => $variant->variant_type_name,
                ];
            }),
            'sku' => $this->sku,
            'isActive' => (bool)$this->is_active,
            'stockQuantity' => $this->stock_quantity,
            'hasVariants' => $this->productTypeVariant && $this->productTypeVariant->isNotEmpty(),
            'productSkus' => ($this->productSkus ?? collect())->map(function ($sku) {
                return [
                    'skuId' => $sku->sku_id,
                    'skuCode' => $sku->sku_code,
                    'price' => $sku->price,
                    'stockQuantity' => $sku->stock_quantity,
                    'isActive' => $sku->is_active,
                    'variantOptions' => ($sku->variantOptions ?? collect())->map(function ($opt) {
                        return [
                            'optionId' => $opt->variant_option_id,
                            'optionName' => $opt->variant_value,
                        ];
                    }),
                ];
            }),
        ];
    }
}