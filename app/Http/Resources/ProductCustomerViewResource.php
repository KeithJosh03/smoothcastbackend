<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCustomerViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $hasVariants = $this->productTypeVariant && $this->productTypeVariant->count() > 0;
        $totalStock = $hasVariants && $this->productSkus 
            ? $this->productSkus->sum('stock_quantity') 
            : $this->stock_quantity;

        return [
            'productId'       => $this->product_id,
            'productTitle'    => $this->product_title,
            'basePrice'       => (string) $this->base_price,
            'description'     => $this->description,
            'features'        => $this->features,
            'specifications'  => $this->specifications,
            'brandName'       => $this->brand?->brand_name,
            'subCategoryName' => $this->subCategory?->sub_category_name,
            'categoryName'    => $this->category?->category_name,
            'hasVariants'     => (bool) $hasVariants,
            'sku'             => $this->sku,
            'stockQuantity'   => $totalStock,
            'inStock'         => $totalStock > 0,

            'productMedias' => $this->images ? $this->images->map(function ($img) use ($baseUrl) {
                return [
                    'mediaId'  => $img->image_id,
                    'imageUrl' => $this->formatUrl($img->image_url, $baseUrl),
                    'isMain'   => (bool) $img->isMain,
                ];
            })->toArray() : [],

            'productVariants' => $this->productTypeVariant ? $this->productTypeVariant->map(function ($vt) use ($baseUrl) {
                return [
                    'variantTypeId'   => $vt->variant_type_id,
                    'variantTypeName' => $vt->variant_name,
                    'variantOptions'  => $vt->variantOptions ? $vt->variantOptions->map(function ($opt) use ($baseUrl) {
                        return [
                            'variantOptionId'    => $opt->variant_option_id,
                            'variantOptionValue' => $opt->variant_value,
                            'variantOptionPrice' => '0',
                            'imageUrl'           => $opt->image ? $this->formatUrl($opt->image->image_url, $baseUrl) : null,
                        ];
                    })->toArray() : [],
                ];
            })->toArray() : null,

            'productSkus' => $this->productSkus ? $this->productSkus->map(function ($sku) use ($baseUrl) {
                return [
                    'skuId'            => $sku->sku_id,
                    'skuCode'          => $sku->sku_code,
                    'price'            => (string) $sku->price,
                    'stockQuantity'    => $sku->stock_quantity,
                    'inStock'          => $sku->stock_quantity > 0,
                    'variantOptionIds' => $sku->variantOptions ? $sku->variantOptions->pluck('variant_option_id')->toArray() : [],
                    'skuImages'        => $sku->images && $sku->images->isNotEmpty() ? $sku->images->map(function ($img) use ($baseUrl) {
                        return [
                            'imageUrl' => $this->formatUrl($img->image_url, $baseUrl),
                            'isMain'   => (bool) $img->isMain,
                        ];
                    })->toArray() : null,
                ];
            })->toArray() : null,
        ];
    }

    private function formatUrl(?string $url, string $baseUrl): ?string
    {
        if (empty($url)) return null;
        if (filter_var($url, FILTER_VALIDATE_URL)) return $url;
        return $baseUrl . '/' . ltrim($url, '/');
    }
}