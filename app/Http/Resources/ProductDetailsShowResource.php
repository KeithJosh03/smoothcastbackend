<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Calculate lowest variant price if base_price is 0
        $matrixPrices = $this->productSkus 
            ? $this->productSkus->pluck('price')->filter(fn($p) => $p > 0) 
            : collect();
        
        $minVariantPrice = $matrixPrices->min() ?? 0;

        $effectiveBasePrice = (float) $this->base_price > 0 
            ? (float) $this->base_price 
            : (float) $minVariantPrice;

        $baseUrl = rtrim(config('app.url'), '/');

        return [
            'productId'      => $this->product_id,
            'productTitle'   => $this->product_title,
            'basePrice'      => $effectiveBasePrice,
            'description'    => $this->description,
            'features'       => $this->features,
            'specifications' => $this->specifications,
            'sku'            => $this->sku,
            'stockQuantity'  => $this->stock_quantity,

            'brand' => [
                'brandId'   => $this->brand_id,
                'brandName' => $this->brand?->brand_name,
            ],
            
            'category' => [
                'categoryId'   => $this->subCategory?->category?->category_id ?? $this->category_id,
                'categoryName' => $this->subCategory?->category?->category_name ?? $this->category?->category_name,
            ],
            'subCategory' => [
                'subCategoryId'   => $this->sub_category_id,
                'subCategoryName' => $this->subCategory?->sub_category_name,
            ],

            'productMedias' => $this->images ? $this->images->map(function ($image) use ($baseUrl) {
                return [
                    'imageId'  => $image->image_id ?? null,
                    'imageUrl' => $this->formatUrl($image->image_url ?? '', $baseUrl),
                    'isMain'   => (bool) ($image->isMain ?? false),
                ];
            }) : [],

            'productVariants' => $this->productTypeVariant ? $this->productTypeVariant->map(function ($type) use ($baseUrl) {
                return [
                    'variantTypeId'   => $type->variant_type_id ?? null,
                    'variantTypeName' => $type->variant_name ?? '',
                    'variantOptions'  => $type->variantOptions ? $type->variantOptions->map(function ($opt) use ($baseUrl) {
                        return [
                            'variantOptionId'    => $opt->variant_option_id ?? null,
                            'variantOptionValue' => $opt->variant_value ?? '',
                            'imageUrl'           => $this->formatUrl($opt->image?->image_url, $baseUrl),
                        ];
                    }) : [],
                ];
            }) : [],

            'variantMatrix' => $this->productSkus ? $this->productSkus->map(function ($sku) use ($baseUrl) {
                $skuUrl = $sku->images?->first()?->image_url ?? null;
                $originalPrice = (float) ($sku->price ?? 0);
                $finalPrice = (float) ($sku->final_price ?? $sku->price ?? 0);
                $activePromo = $sku->active_promotion;

                return [
                    'skuId'               => $sku->sku_id ?? null,
                    'skuCode'             => $sku->sku_code ?? '',
                    'price'               => $originalPrice,
                    'finalPrice'          => $finalPrice,
                    'hasDiscount'         => $finalPrice < $originalPrice,
                    'stockQuantity'       => (int) ($sku->stock_quantity ?? 0),
                    'isActive'            => (bool) ($sku->is_active ?? true),
                    'imageUrl'            => $this->formatUrl($skuUrl, $baseUrl),
                    'promotionInfo'       => $activePromo ? [
                        'id'            => $activePromo->id,
                        'name'          => $activePromo->name,
                        'discountType'  => $activePromo->discount_type,   // PERCENTAGE | FIXED_AMOUNT
                        'discountValue' => (float) $activePromo->discount_value,
                        'applyTo'       => $activePromo->apply_to,        // ALL | CATEGORY | PRODUCT
                    ] : null,
                    'variantOptionIds'    => $sku->variantOptions ? $sku->variantOptions->pluck('variant_option_id')->toArray() : [],
                    'variantOptionValues' => $sku->variantOptions ? $sku->variantOptions->pluck('variant_value')->toArray() : [],
                ];
            }) : [],
        ];
    }

    private function formatUrl(?string $url, string $baseUrl): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return $baseUrl . '/' . ltrim($url, '/');
    }
}