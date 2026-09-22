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
use App\Http\Resources\ProductDetailsShowResource;
use App\Http\Resources\ProductCustomerViewResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $products = Product::paginate($perPage);

        return response()->json([
            'status'   => true,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $hasVariants = $request->has('variants') && is_array($request->input('variants')) && count($request->input('variants')) > 0;

        $validated = $request->validate([
            'product_title'                                   => 'required|string|max:255',
            'base_price'                                      => $hasVariants ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'description'                                     => 'required|string',
            'features'                                        => 'nullable|string',
            'specifications'                                  => 'nullable|string',
            'category_id'                                     => 'required|exists:categories,category_id',
            'sub_category_id'                                 => 'nullable|exists:sub_categories,sub_category_id',
            'brand_id'                                        => 'nullable|exists:brands,brand_id',
            'sku'                                             => $hasVariants ? 'nullable|string' : 'nullable|string|unique:products,sku',
            'stock_quantity'                                  => $hasVariants ? 'nullable|integer' : 'nullable|integer|min:0',
            'medias'                                          => 'nullable|array',
            'medias.*.url'                                    => 'nullable|string',
            'medias.*.file'                                   => 'nullable|string',
            'medias.*.isMain'                                 => 'nullable|boolean',
            'variants'                                        => 'nullable|array',
            'variants.*.variantTypeName'                      => 'nullable|string',
            'variants.*.variantOptions'                       => 'nullable|array',
            'variants.*.variantOptions.*.variantOptionValue' => 'nullable|string',
            'variants.*.variantOptions.*.variant_image'      => 'nullable|string',
            'variants.*.variantOptions.*.imageUrl'           => 'nullable|string',
            'variant_matrix'                                  => 'nullable|array',
            'variant_matrix.*.sku_code'                       => 'required_with:variant_matrix|string|distinct|unique:product_skus,sku_code',
            'variant_matrix.*.price'                          => 'required_with:variant_matrix|numeric|min:0',
            'variant_matrix.*.stock_quantity'                 => 'required_with:variant_matrix|integer|min:0',
            'variant_matrix.*.image_url'                      => 'nullable|string',
            'variant_matrix.*.variant_option_ids'              => 'nullable|array',
            'variant_matrix.*.variant_option_values'          => 'nullable|array'
        ]);

        return DB::transaction(function () use ($validated, $hasVariants) {
            $masterSku = $hasVariants 
                ? null 
                : (!empty($validated['sku']) 
                    ? $validated['sku'] 
                    : 'SKU-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['product_title']), 0, 4)) . '-' . strtoupper(substr(md5(uniqid()), 0, 4)));

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

    public function show($id)
    {
        $product = Product::with([
            'category',
            'subCategory',
            'brand',
            'images',
            'productTypeVariant.variantOptions.image',
            'productSkus.variantOptions',
            'productSkus.images'
        ])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => new ProductDetailsResource($product)
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $hasVariants = $request->has('variants') && is_array($request->input('variants')) && count($request->input('variants')) > 0;

        // Fetch current SKU IDs so validation rule won't block current matrix SKUs being updated
        $currentSkuIds = $product->productSkus()->pluck('sku_id')->toArray();

        $validated = $request->validate([
            'product_title'                                   => 'required|string|max:255',
            'base_price'                                      => $hasVariants ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'description'                                     => 'required|string',
            'features'                                        => 'nullable|string',
            'specifications'                                  => 'nullable|string',
            'category_id'                                     => 'required|exists:categories,category_id',
            'sub_category_id'                                 => 'nullable|exists:sub_categories,sub_category_id',
            'brand_id'                                        => 'nullable|exists:brands,brand_id',
            'sku'                                             => $hasVariants ? 'nullable|string' : 'nullable|string|unique:products,sku,' . $product->product_id . ',product_id',
            'stock_quantity'                                  => $hasVariants ? 'nullable|integer' : 'nullable|integer|min:0',
            'medias'                                          => 'nullable|array',
            'medias.*.url'                                    => 'nullable|string',
            'medias.*.file'                                   => 'nullable|string',
            'medias.*.isMain'                                 => 'nullable|boolean',
            'variants'                                        => 'nullable|array',
            'variants.*.variantTypeName'                      => 'nullable|string',
            'variants.*.variantOptions'                       => 'nullable|array',
            'variants.*.variantOptions.*.variantOptionValue' => 'nullable|string',
            'variants.*.variantOptions.*.variant_image'      => 'nullable|string',
            'variants.*.variantOptions.*.imageUrl'           => 'nullable|string',
            'variant_matrix'                                  => 'nullable|array',
            'variant_matrix.*.sku_code' => [
                'required_with:variant_matrix',
                'string',
                'distinct',
                \Illuminate\Validation\Rule::unique('product_skus', 'sku_code')->where(function ($query) use ($product) {
                    return $query->where('product_id', '!=', $product->product_id);
                }),
            ],
            'variant_matrix.*.price'                          => 'required_with:variant_matrix|numeric|min:0',
            'variant_matrix.*.stock_quantity'                 => 'required_with:variant_matrix|integer|min:0',
            'variant_matrix.*.image_url'                      => 'nullable|string',
            'variant_matrix.*.variant_option_ids'              => 'nullable|array',
            'variant_matrix.*.variant_option_values'          => 'nullable|array'
        ]);

        return DB::transaction(function () use ($product, $validated, $hasVariants) {
            $masterSku = $hasVariants 
                ? null 
                : (!empty($validated['sku']) 
                    ? $validated['sku'] 
                    : 'SKU-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['product_title']), 0, 4)) . '-' . strtoupper(substr(md5(uniqid()), 0, 4)));

            $product->update([
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
            ]);

            $product->images()->delete();
            $this->saveImages($product, $validated['medias'] ?? []);

            $this->syncVariantsAndMatrix($product, $validated, $hasVariants);

            return response()->json([
                'status'  => true,
                'message' => 'Product updated successfully.',
            ]);
        });
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            $product->images()->delete();

            $variantTypeIds = $product->productTypeVariant()->pluck('variant_type_id');
            $optionIds = VariantOptions::whereIn('variant_type_id', $variantTypeIds)->pluck('variant_option_id');
            
            VariantOptions::whereIn('variant_option_id', $optionIds)->delete();
            $product->productTypeVariant()->delete();

            $skuIds = $product->productSkus()->pluck('sku_id');
            if ($skuIds->isNotEmpty()) {
                DB::table('sku_variant_option')->whereIn('sku_id', $skuIds)->delete();
                \App\Models\Image::where('imageable_type', ProductSku::class)
                    ->whereIn('imageable_id', $skuIds)
                    ->delete();
            }
            $product->productSkus()->delete();

            $product->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    public function ProductDetailsEditDashboard($productId)
    {
        $product = Product::with([
            'category',
            'subCategory.category',
            'brand',
            'images',
            'productTypeVariant.variantOptions.image',
            'productSkus.product',
            'productSkus.variantOptions',
            'productSkus.images',
        ])->find($productId);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => new ProductDetailsShowResource($product),
        ], 200);
    }

    public function productViewDetails($id)
    {
        $product = Product::with([
            'category',
            'brand',
            'productSkus.variantOptions',
        ])->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => new ProductCustomerViewResource($product),
        ], 200);
    }

    public function checkSku(Request $request)
    {
        $sku = $request->query('sku');
        $ignoreProductId = $request->query('ignore_product_id');

        if (!$sku) {
            return response()->json(['available' => false, 'message' => 'SKU query parameter is required.'], 400);
        }

        $queryInProducts = Product::where('sku', $sku);
        $queryInSkus = ProductSku::where('sku_code', $sku);

        if ($ignoreProductId) {
            $queryInProducts->where('product_id', '!=', $ignoreProductId);
            $queryInSkus->where('product_id', '!=', $ignoreProductId);
        }

        $exists = $queryInProducts->exists() || $queryInSkus->exists();

        return response()->json([
            'available' => !$exists,
            'exists'    => $exists,
            'message'   => $exists ? 'SKU is already taken.' : 'SKU is available.'
        ]);
    }

    public function validateSku(Request $request)
    {
        $request->validate([
            'baseSku'          => 'required|string',
            'currentProductId' => 'nullable',
        ]);

        $baseSku = trim($request->input('baseSku'));
        $currentProductId = $request->input('currentProductId');

        $uniqueSku = $baseSku;
        $counter = 1;
        $exists = true;

        while ($exists) {
            $queryInProducts = Product::where('sku', $uniqueSku);
            $queryInSkus = ProductSku::where('sku_code', $uniqueSku);

            if ($currentProductId) {
                $queryInProducts->where('product_id', '!=', $currentProductId);
                $queryInSkus->where('product_id', '!=', $currentProductId);
            }

            if (!$queryInProducts->exists() && !$queryInSkus->exists()) {
                $exists = false;
            } else {
                $counter++;
                $uniqueSku = $baseSku . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            }
        }

        return response()->json([
            'sku'         => $uniqueSku,
            'isAvailable' => $uniqueSku === $baseSku,
        ]);
    }

    public function productlistdashboardsearch(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'brand', 'images']);

        if ($request->filled('productTitle')) {
            $query->where('product_title', 'LIKE', '%' . $request->productTitle . '%');
        }

        if ($request->filled('brandId')) {
            $query->where('brand_id', $request->brandId);
        }

        if ($request->filled('categoryId')) {
            $query->where('category_id', $request->categoryId);
        }

        $products = $query->paginate(10);

        return response()->json([
            'status'     => true,
            'products'   => ProductListDashBoardResource::collection($products),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ]
        ]);
    }

    public function productsearch(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'brand', 'images']);

        if ($request->filled('productTitle')) {
            $query->where('product_title', 'LIKE', '%' . $request->productTitle . '%');
        }

        $products = $query->take(10)->get();

        return response()->json([
            'status'   => true,
            'products' => ProductSearchResource::collection($products)
        ]);
    }

    // --- Private Helper Methods ---

    private function saveImages($model, array $medias): void
    {
        $records = [];
        foreach ($medias as $media) {
            $url = $media['url'] ?? $media['file'] ?? null;
            if (!empty($url) && is_string($url)) {
                $records[] = [
                    'image_url' => $url,
                    'isMain'    => $media['isMain'] ?? false,
                ];
            }
        }

        if (!empty($records)) {
            $model->images()->createMany($records);
        }
    }

    private function processVariantsAndMatrix($product, array $validated, bool $hasVariants): void
    {
        if (!$hasVariants) {
            return;
        }

        $optionValueMap = [];

        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $vData) {
                $variantType = $product->productTypeVariant()->create([
                    'variant_name' => $vData['variantTypeName'] ?? '',
                ]);

                if (!empty($vData['variantOptions'])) {
                    foreach ($vData['variantOptions'] as $optData) {
                        $optionValue = $optData['variantOptionValue'] ?? '';
                        $option = $variantType->variantOptions()->create([
                            'variant_value' => $optionValue,
                        ]);

                        $optImg = $optData['variant_image'] ?? $optData['imageUrl'] ?? null;
                        if (!empty($optImg) && is_string($optImg)) {
                            $option->image()->create([
                                'image_url' => $optImg,
                                'isMain'    => true,
                            ]);
                        }

                        if (!empty($optionValue)) {
                            $optionValueMap[strtolower(trim($optionValue))] = $option->variant_option_id;
                        }
                    }
                }
            }
        }

        if (!empty($validated['variant_matrix'])) {
            // Collect fallback option IDs for single batch existence query
            $allPossibleIds = [];
            foreach ($validated['variant_matrix'] as $mRow) {
                if (!empty($mRow['variant_option_ids'])) {
                    foreach ($mRow['variant_option_ids'] as $possibleId) {
                        if (is_numeric($possibleId)) {
                            $allPossibleIds[] = (int) $possibleId;
                        }
                    }
                }
            }

            $validOptionIds = !empty($allPossibleIds)
                ? VariantOptions::whereIn('variant_option_id', array_unique($allPossibleIds))->pluck('variant_option_id')->toArray()
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
                $optionsToMatch = $mRow['variant_option_values'] ?? [];

                foreach ($optionsToMatch as $val) {
                    $cleanVal = is_array($val) ? strtolower(trim($val['value'] ?? '')) : strtolower(trim((string) $val));
                    if (isset($optionValueMap[$cleanVal])) {
                        $realIdsToSync[] = $optionValueMap[$cleanVal];
                    }
                }

                if (empty($realIdsToSync) && !empty($mRow['variant_option_ids'])) {
                    foreach ($mRow['variant_option_ids'] as $possibleId) {
                        if (in_array((int) $possibleId, $validOptionIds, true)) {
                            $realIdsToSync[] = (int) $possibleId;
                        }
                    }
                }

                if (!empty($realIdsToSync)) {
                    $skuRecord->variantOptions()->sync(array_unique($realIdsToSync));
                }
            }
        }
    }

    protected function syncVariantsAndMatrix($product, $validatedData, $hasVariants)
    {
        $variantTypeIds = $product->productTypeVariant()->pluck('variant_type_id');
        $optionIds = VariantOptions::whereIn('variant_type_id', $variantTypeIds)->pluck('variant_option_id');

        VariantOptions::whereIn('variant_option_id', $optionIds)->delete();
        $product->productTypeVariant()->delete();

        $skuIds = $product->productSkus()->pluck('sku_id');
        if ($skuIds->isNotEmpty()) {
            DB::table('sku_variant_option')->whereIn('sku_id', $skuIds)->delete();
            \App\Models\Image::where('imageable_type', ProductSku::class)
                ->whereIn('imageable_id', $skuIds)
                ->delete();
        }
        $product->productSkus()->delete();

        if (!$hasVariants) {
            return;
        }

        $this->processVariantsAndMatrix($product, $validatedData, $hasVariants);
    }
}