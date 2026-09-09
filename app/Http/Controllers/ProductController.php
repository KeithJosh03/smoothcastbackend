<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSku;
use App\Models\VariantOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductSearchResource;
use App\Http\Resources\ProductListDashBoardResource;
use App\Http\Resources\NewArrivalResource;
use App\Http\Resources\ProductDetailsShowResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::get();
        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        $hasVariants = $request->has('variants') && is_array($request->input('variants')) && count($request->input('variants')) > 0;

        $validated = $request->validate([
            'product_title'             => 'required|string|max:255',
            'base_price'                => $hasVariants ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'description'               => 'required|string',
            'features'                  => 'nullable|string',
            'specifications'            => 'nullable|string',
            'category_id'               => 'required|exists:categories,category_id',
            'sub_category_id'           => 'nullable|exists:sub_categories,sub_category_id',
            'brand_id'                  => 'nullable|exists:brands,brand_id',
            'sku'                       => $hasVariants ? 'nullable|string' : 'nullable|string|unique:products,sku',
            'stock_quantity'            => 'nullable|integer',
            'medias'                    => 'nullable|array',
            'medias.*.url'              => 'nullable|string',
            'medias.*.file'             => 'nullable|string',
            'medias.*.isMain'           => 'nullable|boolean',
            'variants'                  => 'nullable|array',
            'variant_matrix'            => 'nullable|array',
            'variant_matrix.*.sku_code' => 'required_with:variant_matrix|string|distinct|unique:product_skus,sku_code'
        ]);

        return DB::transaction(function () use ($validated, $hasVariants) {
            $masterSku = !empty($validated['sku'])
                ? $validated['sku']
                : (!$hasVariants
                    ? 'SKU-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['product_title']), 0, 4)) . '-' . strtoupper(substr(md5(uniqid()), 0, 4))
                    : null);

            $product = Product::create([
                'product_title'   => $validated['product_title'],
                'base_price'      => $validated['base_price'] ?? 0.00,
                'description'     => $validated['description'],
                'features'        => $validated['features'] ?? null,
                'specifications'  => $validated['specifications'] ?? null,
                'category_id'     => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'brand_id'        => $validated['brand_id'] ?? null,
                'sku'             => $masterSku,
                'stock_quantity'  => $hasVariants ? null : ($validated['stock_quantity'] ?? 0),
                'release_date'    => Carbon::now(),
            ]);

            $this->saveImages($product, $validated['medias'] ?? []);
            $this->processVariantsAndMatrix($product, $validated, $hasVariants);

            return response()->json([
                'status'     => true,
                'message'    => 'Product created successfully.',
                'product_id' => $product->product_id,
            ], 201);
        });
    }

    public function show(Product $product)
    {
        return $this->productSpecificDetail($product->product_id);
    }

    public function edit(Product $product)
    {
        return $this->productSpecificDetail($product->product_id);
    }

    public function update(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $hasVariants = $request->has('variants') 
            && is_array($request->input('variants')) 
            && count($request->input('variants')) > 0;

        // 1. Validation
        $validated = $request->validate([
            'product_title'             => 'required|string|max:255',
            'base_price'                => $hasVariants ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'description'               => 'required|string',
            'features'                  => 'nullable|string',
            'specifications'            => 'nullable|string',
            'category_id'               => 'required|exists:categories,category_id',
            'sub_category_id'           => 'nullable|exists:sub_categories,sub_category_id',
            'brand_id'                  => 'nullable|exists:brands,brand_id',
            'sku'                       => $hasVariants ? 'nullable|string' : 'required|string|unique:products,sku,' . $product->product_id . ',product_id',
            'stock_quantity'            => $hasVariants ? 'nullable|integer' : 'required|integer|min:0',
            'medias'                    => 'nullable|array',
            'medias.*.url'              => 'nullable|string',
            'medias.*.file'             => 'nullable|string',
            'medias.*.isMain'           => 'nullable|boolean',
            'variants'                  => 'nullable|array',
            'variant_matrix'            => 'nullable|array',
            'variant_matrix.*.sku_code' => 'required_with:variant_matrix|string|distinct',
            'variant_matrix.*.price'    => 'required_with:variant_matrix|numeric|min:0',
            'variant_matrix.*.stock_quantity' => 'required_with:variant_matrix|integer|min:0',
        ]);

        return DB::transaction(function () use ($validated, $product, $hasVariants) {

            // 2. Update Master Product Info
            $product->update([
                'product_title'   => $validated['product_title'],
                'base_price'      => $validated['base_price'] ?? 0.00,
                'description'     => $validated['description'],
                'features'        => $validated['features'] ?? null,
                'specifications'  => $validated['specifications'] ?? null,
                'category_id'     => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'brand_id'        => $validated['brand_id'] ?? null,
                // Simple Product: Keeps top-level SKU & Stock
                // Variant Product: Sets top-level SKU & Stock to null
                'sku'             => $hasVariants ? null : $validated['sku'],
                'stock_quantity'  => $hasVariants ? null : $validated['stock_quantity'],
            ]);

            // 3. Update Product Images
            if (isset($validated['medias'])) {
                $product->images()->delete();
                $this->saveImages($product, $validated['medias']);
            }

            // 4. Handle Variant Transition State Changes
            if ($hasVariants) {
                // Processing Variant Product -> Variant Product OR Simple -> Variant
                $this->syncVariantsAndMatrix($product, $validated);
            } else {
                // Processing Variant Product -> Simple Product
                // Soft-deactivate previous variant SKUs to preserve sales history integrity
                foreach ($product->productSkus as $sku) {
                    $sku->update(['is_active' => false]);
                }
            }

            return response()->json([
                'status'     => true,
                'message'    => $hasVariants ? 'Variant product updated successfully.' : 'Simple product updated successfully.',
                'product_id' => $product->product_id,
            ]);
        });
    }

    public function destroy($productId)
    {
        return DB::transaction(function () use ($productId) {
            $product = Product::findOrFail($productId);
            
            // Delete related models to prevent foreign key issues
            $product->images()->delete();
            foreach ($product->productTypeVariant as $vType) {
                foreach ($vType->variantOptions as $vOpt) {
                    $vOpt->image()->delete();
                    $vOpt->delete();
                }
                $vType->delete();
            }
            foreach ($product->productSkus as $sku) {
                $sku->variantOptions()->detach();
                $sku->images()->delete();
                $sku->delete();
            }
            
            $product->delete();
            return response()->json(null, 204);
        });
    }

    public function checkSku(Request $request)
    {
        $sku = trim($request->query('sku', ''));
        $ignoreProductId = $request->query('ignore_product_id');

        if (empty($sku)) {
            return response()->json([
                'sku'       => $sku,
                'available' => false,
                'exists'    => false,
                'message'   => 'SKU query parameter is required.'
            ], 400);
        }

        $productQuery = Product::where('sku', $sku);
        $variantQuery = ProductSku::where('sku_code', $sku);

        if (!empty($ignoreProductId)) {
            $productQuery->where('product_id', '!=', $ignoreProductId);
            $variantQuery->whereHas('product', function ($q) use ($ignoreProductId) {
                $q->where('product_id', '!=', $ignoreProductId);
            });
        }

        $exists = $productQuery->exists() || $variantQuery->exists();

        return response()->json([
            'sku'       => $sku,
            'available' => !$exists,
            'exists'    => $exists,
            'message'   => !$exists ? 'SKU is available.' : 'SKU is already taken.'
        ], 200);
    }

    public function productViewDetails($productId)
    {
        $productdetail = Product::select(
            'product_id', 'sub_category_id', 'category_id', 'brand_id',
            'product_title', 'base_price', 'description', 'features',
            'specifications', 'sku', 'stock_quantity'
        )
        ->with([
            'brand:brand_id,brand_name',
            'subCategory:sub_category_name,sub_category_id',
            'category:category_id,category_name',
            'images',
            'productTypeVariant:product_id,variant_type_id,variant_name',
            'productTypeVariant.variantOptions:variant_type_id,variant_option_id,variant_value',
            'productSkus' => function ($query) {
                $query->where('is_active', true)
                    ->select('sku_id', 'product_id', 'sku_code', 'price', 'stock_quantity', 'is_active')
                    ->with(['images', 'variantOptions']);
            }
        ])
        ->where('product_id', $productId)
        ->first();

        if (!$productdetail) {
            return response()->json([
                'status' => true,
                'productdetail' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'productdetail' => new ProductDetailsShowResource($productdetail)
        ]);
    }

    public function productSearch(Request $request)
    {
        $productname = trim($request->query('productTitle', ''));
        $query = Product::select('product_id', 'product_title');

        if (empty($productname)) {
            $products = $query->inRandomOrder()->limit(10)->get();
        } else {
            $products = $query->whereRaw('LOWER(product_title) LIKE ?', ['%' . strtolower($productname) . '%'])->get();
        }

        return response()->json([
            'status' => true,
            'products' => ProductSearchResource::collection($products)
        ]);
    }

    public function productListDashBoardSearch(Request $request)
    {
        $search = $request->query('productTitle');
        $brandId = $request->query('brandId');
        $categoryId = $request->query('categoryId');

        $query = Product::query()
            ->when($search, fn($q) => $q->whereRaw('LOWER(product_title) LIKE ?', ['%' . strtolower($search) . '%']))
            ->when($brandId, fn($q) => $q->where('brand_id', $brandId))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->select('product_id', 'product_title', 'base_price', 'category_id', 'sub_category_id', 'brand_id', 'sku', 'stock_quantity')
            ->with([
                'category:category_id,category_name',
                'brand:brand_id,brand_name',
                'subCategory:sub_category_id,sub_category_name',
                'productTypeVariant:product_id,variant_type_id,variant_name',
                'productSkus' => function ($query) {
                    $query->where('is_active', true)
                        ->select('sku_id', 'product_id', 'sku_code', 'price', 'stock_quantity', 'is_active')
                        ->with(['variantOptions:variant_option_id,variant_value']);
                }
            ])
            ->orderBy('product_id', 'desc');

        $products = $query->paginate(10);

        return response()->json([
            'status' => true,
            'products' => ProductListDashBoardResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    public function ProductDetailsEditDashboard($productId)
    {
        $productdetail = Product::select(
            'product_id',
            'sub_category_id',
            'category_id',
            'brand_id',
            'product_title',
            'base_price',
            'description',
            'features',
            'specifications',
            'sku',            // <-- Needed for Simple Product SKU
            'stock_quantity'  // <-- Needed for Simple Product Stock
        )
        ->with([
            'brand',
            'subCategory',
            'category',
            'images',
            'productTypeVariant.variantOptions.image',
            'productSkus' => function ($query) {
                $query->where('is_active', true)
                    ->with(['images', 'variantOptions']);
            }
        ])
        ->where('product_id', $productId)
        ->first();

        if (!$productdetail) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'productdetail' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'productdetail' => new ProductDetailsResource($productdetail)
        ]);
    }

    public function deleteProduct($productId)
    {
        return $this->destroy($productId);
    }

    // --- Private Helper Methods ---

    private function saveImages($model, array $medias): void
    {
        foreach ($medias as $media) {
            $url = $media['url'] ?? $media['file'] ?? null;
            if (!empty($url) && is_string($url)) {
                $model->images()->create([
                    'image_url' => $url,
                    'isMain'    => $media['isMain'] ?? false,
                ]);
            }
        }
    }

    private function processVariantsAndMatrix($product, array $validated, bool $hasVariants): void
    {
        if (!$hasVariants) {
            return;
        }

        $optionValueMap = [];
        $positionalOptionMap = [];

        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $vIndex => $vData) {
                $variantType = $product->productTypeVariant()->create([
                    'variant_name' => $vData['variantTypeName'] ?? '',
                ]);

                if (!empty($vData['variantOptions'])) {
                    foreach ($vData['variantOptions'] as $optIndex => $optData) {
                        $option = $variantType->variantOptions()->create([
                            'variant_value' => $optData['variantOptionValue'] ?? '',
                        ]);

                        if (!empty($optData['variant_image']) && is_string($optData['variant_image'])) {
                            $option->image()->create([
                                'image_url' => $optData['variant_image'],
                                'isMain'    => true,
                            ]);
                        }

                        $optionValueMap[$optData['variantOptionValue']] = $option->variant_option_id;
                        $positionalOptionMap[$vIndex][$optIndex + 1] = $option->variant_option_id;
                    }
                }
            }
        }

        if (!empty($validated['variant_matrix'])) {
            // Bulk fetch numeric IDs to eliminate N+1 queries
            $numericIds = [];
            foreach ($validated['variant_matrix'] as $mRow) {
                if (!empty($mRow['variant_option_ids'])) {
                    foreach ($mRow['variant_option_ids'] as $rawOptId) {
                        if (is_numeric($rawOptId)) {
                            $numericIds[] = (int)$rawOptId;
                        }
                    }
                }
            }

            $validNumericIds = !empty($numericIds)
                ? VariantOptions::whereIn('variant_option_id', array_unique($numericIds))->pluck('variant_option_id')->flip()->toArray()
                : [];

            foreach ($validated['variant_matrix'] as $mRow) {
                $skuRecord = $product->productSkus()->create([
                    'sku_code'       => $mRow['sku_code'],
                    'price'          => $mRow['price'] ?? 0.00,
                    'stock_quantity' => $mRow['stock_quantity'] ?? 0,
                    'is_active'      => true,
                ]);

                if (!empty($mRow['image_url']) && is_string($mRow['image_url'])) {
                    $skuRecord->images()->create([
                        'image_url' => $mRow['image_url'],
                        'isMain'    => true,
                    ]);
                }

                $realIdsToSync = [];
                if (!empty($mRow['variant_option_ids'])) {
                    foreach ($mRow['variant_option_ids'] as $vIdx => $rawOptId) {
                        if (isset($optionValueMap[$rawOptId])) {
                            $realIdsToSync[] = $optionValueMap[$rawOptId];
                        } elseif (isset($positionalOptionMap[$vIdx][$rawOptId])) {
                            $realIdsToSync[] = $positionalOptionMap[$vIdx][$rawOptId];
                        } elseif (is_numeric($rawOptId) && isset($validNumericIds[$rawOptId])) {
                            $realIdsToSync[] = $rawOptId;
                        }
                    }
                }

                if (!empty($realIdsToSync)) {
                    $skuRecord->variantOptions()->sync(array_unique($realIdsToSync));
                }
            }
        }
    }

    private function syncVariantsAndMatrix($product, array $validated): void
    {
        // Clear old active variants definitions for re-building clean options
        foreach ($product->productTypeVariant as $vType) {
            foreach ($vType->variantOptions as $vOpt) {
                $vOpt->image()->delete();
                $vOpt->delete();
            }
            $vType->delete();
        }

        $optionValueMap = [];

        // 1. Re-create Variant Types and Options
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $vData) {
                $variantType = $product->productTypeVariant()->create([
                    'variant_name' => $vData['variantTypeName'] ?? '',
                ]);

                if (!empty($vData['variantOptions'])) {
                    foreach ($vData['variantOptions'] as $optData) {
                        $option = $variantType->variantOptions()->create([
                            'variant_value' => $optData['variantOptionValue'] ?? '',
                        ]);

                        if (!empty($optData['imageUrl'])) {
                            $option->image()->create([
                                'image_url' => $optData['imageUrl'],
                                'isMain'    => true,
                            ]);
                        }

                        // Map Option Value string to new DB ID
                        $optionValueMap[$optData['variantOptionValue']] = $option->variant_option_id;
                    }
                }
            }
        }

        // 2. Sync Matrix SKUs
        if (!empty($validated['variant_matrix'])) {
            $existingSkus = $product->productSkus->keyBy('sku_id');
            $incomingSkuIds = [];

            foreach ($validated['variant_matrix'] as $mRow) {
                $skuId = $mRow['sku_id'] ?? null;

                if ($skuId && $existingSkus->has($skuId)) {
                    // Update existing SKU row (Preserves ID for sales history)
                    $skuRecord = $existingSkus->get($skuId);
                    $skuRecord->update([
                        'sku_code'       => $mRow['sku_code'],
                        'price'          => $mRow['price'] ?? 0.00,
                        'stock_quantity' => $mRow['stock_quantity'] ?? 0,
                        'is_active'      => true,
                    ]);
                    $incomingSkuIds[] = $skuId;
                } else {
                    // Create new SKU row
                    $skuRecord = $product->productSkus()->create([
                        'sku_code'       => $mRow['sku_code'],
                        'price'          => $mRow['price'] ?? 0.00,
                        'stock_quantity' => $mRow['stock_quantity'] ?? 0,
                        'is_active'      => true,
                    ]);
                    $incomingSkuIds[] = $skuRecord->sku_id;
                }

                // Sync Pivot Option Links
                $realOptionIds = [];
                if (!empty($mRow['variant_option_values'])) {
                    foreach ($mRow['variant_option_values'] as $val) {
                        if (isset($optionValueMap[$val])) {
                            $realOptionIds[] = $optionValueMap[$val];
                        }
                    }
                }
                $skuRecord->variantOptions()->sync($realOptionIds);
            }

            // Soft-deactivate SKUs omitted from updated matrix
            $product->productSkus()
                ->whereNotIn('sku_id', $incomingSkuIds)
                ->update(['is_active' => false]);
        }
    }
}