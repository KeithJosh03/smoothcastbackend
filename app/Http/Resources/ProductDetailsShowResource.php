<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 🚨 FIX 1: Changed $this->skus to $this->productSkus to match relation name
        $hasVariants = $this->productSkus && $this->productSkus->count() > 0;

        return [
            'productId'        => $this->product_id,
            'productTitle'     => $this->product_title,
            'basePrice'        => $this->base_price,
            'specifications'   => $this->specifications,
            'features'         => $this->features,
            'description'      => $this->description,
            'brandName'        => $this->brand->brand_name ?? null,
            'subCategoryName'  => $this->subCategories->sub_category_name ?? null,
            'hasVariants'      => $hasVariants,
            
            // SIMPLE PRODUCT INVENTORY DATA
            'sku'              => $this->sku,
            'stockQuantity'    => $hasVariants ? null : (int)$this->stock_quantity,
            'inStock'          => $hasVariants 
                                    ? $this->productSkus->sum('stock_quantity') > 0 
                                    : ((int)$this->stock_quantity > 0),

            // Global Media Gallery
            'productMedias'    => $this->images->map(fn($img) => [
                'imageUrl' => $img->image_url,
                'isMain'   => (bool)$img->isMain,
            ]),

            // Variant Options UI Pills
            'productVariants'  => $this->productTypeVariant->map(fn($type) => [
                'variantTypeId'   => $type->variant_type_id,
                'variantTypeName' => $type->variant_name,
                'variantOptions'  => $type->variantOptions->map(fn($opt) => [
                    'variantOptionId'    => $opt->variant_option_id,
                    'variantOptionValue' => $opt->variant_value,
                    'priceAdjustment'    => "0.00", // 🚨 FIX 2: Safely hardcoded string instead of querying column
                ]),
            ]),

            // 🚨 VARIANT MATRIX SKUS (Matches 'productSkus' key expected by frontend)
            'productSkus' => $this->productSkus->map(fn($sku) => [
                'skuId'            => $sku->sku_id,
                'skuCode'          => $sku->sku_code,
                'price'            => $sku->price,
                'stockQuantity'    => (int)$sku->stock_quantity, 
                'inStock'          => (int)$sku->stock_quantity > 0,
                'variantOptionIds' => $sku->variantOptions->pluck('variant_option_id'),
                'skuImages'        => $sku->images->map(fn($img) => [
                    'imageUrl' => $img->image_url,
                    'isMain'   => (bool)$img->isMain,
                ]),
            ]),
        ];
    }
}