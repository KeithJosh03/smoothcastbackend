<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetupCustomerViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $bundlePrice = (float) $this->bundle_price;
        $retail = $this->retail_price !== null ? (float) $this->retail_price : null;
        $hasDiscount = ($retail && $retail > $bundlePrice) || (!empty($this->discount_percentage) && (float) $this->discount_percentage > 0);

        $discountLabel = null;
        if (!empty($this->discount_percentage) && (float) $this->discount_percentage > 0) {
            $discountLabel = '-' . round((float) $this->discount_percentage) . '%';
        } elseif ($retail && $retail > $bundlePrice) {
            $discountLabel = '-₱' . number_format($retail - $bundlePrice, 2);
        }

        $compareAt = ($retail && $retail > $bundlePrice) ? $retail : null;

        return [
            'setupId' => $this->setup_id,
            'bundleTitle' => $this->bundle_title,
            'slug' => $this->slug,
            'description' => $this->description,
            'sku' => $this->sku,
            'pricingType' => $this->pricing_type,
            'bundlePrice' => (string) $this->bundle_price,
            'retailPrice' => $this->retail_price !== null ? (string) $this->retail_price : null,
            'finalPrice' => (string) $this->bundle_price,
            'compareAtPrice' => $compareAt !== null ? (string) $compareAt : null,
            'hasDiscount' => $hasDiscount,
            'discountLabel' => $discountLabel,
            'stockQuantity' => (int) $this->stock_quantity,
            'inStock' => (int) $this->stock_quantity > 0,
            'categoryName' => $this->category?->name,

            'setupMedias' => $this->images ? $this->images->map(function ($img) use ($baseUrl) {
                return [
                    'mediaId' => $img->image_id,
                    'imageUrl' => $this->formatUrl($img->image_url, $baseUrl),
                    'isMain' => (bool) $img->isMain,
                ];
            })->values()->toArray() : [],

            'bundleItems' => $this->items ? $this->items->map(function ($item) use ($baseUrl) {
                $thumb = $item->product?->images?->firstWhere('isMain', true)
                    ?? $item->product?->images?->first();

                return [
                    'setupItemId' => $item->id,
                    'productId' => $item->product_id,
                    'productTitle' => $item->product?->product_title,
                    'skuId' => $item->sku_id,
                    'skuCode' => $item->sku?->sku_code,
                    'quantity' => (int) $item->quantity,
                    'isRequired' => (bool) $item->is_required,
                    'groupName' => $item->group_name,
                    'thumbnailUrl' => $thumb ? $this->formatUrl($thumb->image_url, $baseUrl) : null,
                ];
            })->values()->toArray() : [],

            'inclusions' => $this->inclusions ? $this->inclusions->map(function ($inc) {
                return [
                    'title' => $inc->title,
                    'price' => (string) $inc->price,
                    'quantity' => (int) $inc->quantity,
                    'isRequired' => (bool) $inc->is_required,
                ];
            })->values()->toArray() : [],
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
