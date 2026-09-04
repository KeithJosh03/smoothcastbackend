<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariantType;
use App\Models\VariantOptions;
use App\Models\ProductSku; 

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductSearchResource;
use App\Http\Resources\ProductDetailInitialResource;
use App\Http\Resources\productListDashBoardResource;
use App\Http\Resources\NewArrivalResource;
use App\Http\Resources\ProductDetailsShowResource;


class ProductController extends Controller
{
    public function index()
    {
        // $products = Product::paginate(15);
        $products = Product::get();
        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }

    public function create()
    {

    }

    public function store(Request $request) 
    {
        // 1. Establish structural state condition flag
        // Evaluates true ONLY if variants key exists AND contains items
        $hasVariants = $request->has('variants') && is_array($request->input('variants')) && count($request->input('variants')) > 0;

        // 2. Validate incoming request parameters
        $validatedData = $request->validate([
            'product_title'   => 'required|string|max:100',
            'base_price'      => 'required|numeric|min:0.00',
            'brand_id'        => 'nullable|exists:brands,brand_id',
            'category_id'     => 'required|exists:categories,category_id',
            'sub_category_id' => 'nullable|exists:sub_categories,sub_category_id',
            'description'     => 'nullable|string',
            'features'        => 'nullable|string',
            'specifications'  => 'nullable|string',
            
            // Global Media Gallery Items
            'medias'          => 'nullable|array',
            'medias.*.url'    => 'required_with:medias|string',
            'medias.*.isMain' => 'nullable|boolean',

            // Raw variant configuration definitions payload array
            'variants'        => 'nullable|array',
            
            // BRANCH A: Simple Product Inventory Setup (Required ONLY if there are no variations)
            'sku'             => [
                $hasVariants ? 'nullable' : 'required',
                'string',
                'unique:products,sku'
            ],
            'stock_quantity'  => [
                $hasVariants ? 'nullable' : 'required',
                'integer',
                'min:0'
            ],

            // BRANCH B: Variant Matrix Generation Setup (Required ONLY if variants are active)
            'variant_matrix'                        => $hasVariants ? 'required|array|min:1' : 'nullable|array',
            'variant_matrix.*.sku_code'             => $hasVariants ? 'required|string|unique:product_skus,sku_code' : 'nullable|string',
            'variant_matrix.*.price'                => $hasVariants ? 'required|numeric|min:0.00' : 'nullable|numeric',
            'variant_matrix.*.stock_quantity'       => $hasVariants ? 'required|integer|min:0' : 'nullable|integer',
            'variant_matrix.*.variant_option_ids'   => $hasVariants ? 'required|array|min:1' : 'nullable|array',
            'variant_matrix.*.variant_option_ids.*' => 'integer',
            'variant_matrix.*.image_url'            => 'nullable|string', // 🚨 ADDED: Validates parallel uploaded row photos
        ]);

        DB::beginTransaction();
        try {
            // 3. Map foundational parent catalog elements
            $productData = [
                'brand_id'        => $validatedData['brand_id'] ?? null,
                'category_id'     => $validatedData['category_id'],
                'sub_category_id' => $validatedData['sub_category_id'] ?? null,
                'product_title'   => $validatedData['product_title'],
                'base_price'      => $validatedData['base_price'],
                'description'     => $validatedData['description'] ?? null,
                'features'        => $validatedData['features'] ?? null,
                'specifications'  => $validatedData['specifications'] ?? null,
                'release_date'    => \Carbon\Carbon::now(), 
            ];

            // 4. Implement Inventory Fork Strategy
            if (!$hasVariants) {
                $productData['sku'] = $validatedData['sku'];
                $productData['stock_quantity'] = $validatedData['stock_quantity'];
            } else {
                $productData['sku'] = null;
                $productData['stock_quantity'] = null;
            }

            // 5. Create Parent Record
            $product = Product::create($productData);

            // 6. Process Global Polymorphic Media Attachments
            if (!empty($validatedData['medias'])) {
                foreach ($validatedData['medias'] as $media) {
                    if (!empty($media['url'])) {
                        $product->images()->create([
                            'image_url' => $media['url'],
                            'isMain'    => $media['isMain'] ?? false,
                        ]);
                    }
                }
            }

            // 7. Branch B: Iterate through dynamic matrix variant combinations
            if ($hasVariants) {
                $realOptionIds = []; // Map: $realOptionIds[variantIndex][simulatedId] = real_variant_option_id

                if (!empty($validatedData['variants'])) {
                    foreach ($validatedData['variants'] as $vIndex => $variantData) {
                        $newVariantType = ProductVariantType::create([
                            'product_id' => $product->product_id,
                            'variant_name' => $variantData['variantTypeName'] ?? '',
                        ]);

                        if (!empty($variantData['variantOptions'])) {
                            foreach ($variantData['variantOptions'] as $optIndex => $optData) {
                                $newOption = $newVariantType->variantOptions()->create([
                                    'variant_value' => $optData['variantOptionValue'] ?? '',
                                    'price_adjustment' => $optData['price_adjustment'] ?? 0,
                                ]);

                                if (!empty($optData['variant_image'])) {
                                    $newOption->image()->create([
                                        'image_url' => $optData['variant_image'],
                                        'isMain' => true,
                                    ]);
                                }
                                
                                // Map the simulated ID (optIndex + 1) to real ID
                                $simulatedId = $optIndex + 1;
                                $realOptionIds[$vIndex][$simulatedId] = $newOption->variant_option_id;
                            }
                        }
                    }
                }

                foreach ($validatedData['variant_matrix'] as $matrixRow) {
                    // Save standalone item SKU configuration row
                    $productSku = ProductSku::create([
                        'product_id'     => $product->product_id,
                        'sku_code'       => $matrixRow['sku_code'],
                        'price'          => $matrixRow['price'],
                        'stock_quantity' => $matrixRow['stock_quantity'],
                        'is_active'      => true
                    ]);

                    // Attach row-specific image polymorphically directly to the SKU if it exists!
                    if (!empty($matrixRow['image_url'])) {
                        $productSku->images()->create([
                            'image_url' => $matrixRow['image_url'],
                            'isMain'    => true, // Acts as the main cover photo for this model combination
                        ]);
                    }

                    // Map simulated IDs to real IDs for syncing
                    $realIdsToSync = [];
                    if (!empty($matrixRow['variant_option_ids'])) {
                        foreach ($matrixRow['variant_option_ids'] as $typeIndex => $simulatedId) {
                            if (isset($realOptionIds[$typeIndex][$simulatedId])) {
                                $realIdsToSync[] = $realOptionIds[$typeIndex][$simulatedId];
                            }
                        }
                    }

                    // Sync the specific option components via pivot bridge
                    $productSku->variantOptions()->sync($realIdsToSync);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'product_id' => $product->product_id,
                'mode' => $hasVariants ? 'variant_matrix' : 'simple'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }






    public function show(Product $product)
    {
        dd($product);
        return $product;
    }

    public function edit(Product $product)
    {

    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            // Scalar fields
            'product_title' => ['sometimes', 'string', 'max:100'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            'features' => ['sometimes', 'nullable', 'string'],
            'specifications' => ['sometimes', 'nullable', 'string'],
            'brand_id' => ['sometimes', 'nullable', 'exists:brands,brand_id'],
            'category_id' => ['sometimes', 'exists:categories,category_id'],
            'sub_category_id' => ['sometimes', 'nullable', 'exists:sub_categories,sub_category_id'],

            // Medias
            'medias' => ['sometimes', 'array'],
            'medias.*.image_id' => ['sometimes', 'nullable', 'integer'],
            'medias.*.url' => ['sometimes', 'nullable', 'string'],
            'medias.*.isMain' => ['sometimes', 'nullable', 'boolean'],
            'removed_media_ids' => ['sometimes', 'array'],
            'removed_media_ids.*' => ['integer'],

            // Variants
            'variants' => ['sometimes', 'array'],
            'variants.*.variant_type_id' => ['sometimes', 'nullable', 'integer'],
            'variants.*.variant_type_name' => ['sometimes', 'nullable', 'string'],
            'variants.*.variant_options' => ['sometimes', 'array'],
            'variants.*.variant_options.*.variant_option_id' => ['sometimes', 'nullable', 'integer'],
            'variants.*.variant_options.*.variant_option_value' => ['sometimes', 'nullable', 'string'],
            'variants.*.variant_options.*.price_adjustment' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'variants.*.variant_options.*.image_url' => ['sometimes', 'nullable', 'string'],
            'removed_variant_type_ids' => ['sometimes', 'array'],
            'removed_variant_type_ids.*' => ['integer'],
            'removed_variant_option_ids' => ['sometimes', 'array'],
            'removed_variant_option_ids.*' => ['integer'],
        ]);

        DB::beginTransaction();

        try {
            // ── 1. Update scalar product fields ──
            $scalarFields = array_intersect_key($validated, array_flip([
                'product_title',
                'base_price',
                'description',
                'features',
                'specifications',
                'brand_id',
                'category_id',
                'sub_category_id',
            ]));
            if (!empty($scalarFields)) {
                $product->update($scalarFields);
            }

            // ── 2. Handle removed medias ──
            if (!empty($validated['removed_media_ids'])) {
                $imagesToDelete = Image::whereIn('image_id', $validated['removed_media_ids'])
                    ->where('imageable_type', Product::class)
                    ->where('imageable_id', $product->product_id)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    // Delete from S3 if the URL is an S3 path
                    if ($img->image_url && Storage::disk('s3')->exists($img->image_url)) {
                        Storage::disk('s3')->delete($img->image_url);
                    }
                    $img->delete();
                }
            }

            // ── 3. Handle media updates / additions ──
            if (!empty($validated['medias'])) {
                foreach ($validated['medias'] as $media) {
                    if (!empty($media['image_id'])) {
                        // Existing media — update isMain if provided
                        $existingImage = Image::where('image_id', $media['image_id'])
                            ->where('imageable_type', Product::class)
                            ->where('imageable_id', $product->product_id)
                            ->first();

                        if ($existingImage && isset($media['isMain'])) {
                            $existingImage->update(['isMain' => $media['isMain']]);
                        }
                    } elseif (!empty($media['url'])) {
                        // New media — create
                        $product->images()->create([
                            'image_url' => $media['url'],
                            'isMain' => $media['isMain'] ?? false,
                        ]);
                    }
                }
            }

            // ── 4. Handle removed variant types (cascade deletes options + images) ──
            if (!empty($validated['removed_variant_type_ids'])) {
                $variantTypesToDelete = ProductVariantType::whereIn('variant_type_id', $validated['removed_variant_type_ids'])
                    ->where('product_id', $product->product_id)
                    ->get();

                foreach ($variantTypesToDelete as $vt) {
                    foreach ($vt->variantOptions as $opt) {
                        // Delete option image from S3
                        if ($opt->image && $opt->image->image_url) {
                            if (Storage::disk('s3')->exists($opt->image->image_url)) {
                                Storage::disk('s3')->delete($opt->image->image_url);
                            }
                            $opt->image->delete();
                        }
                        $opt->delete();
                    }
                    $vt->delete();
                }
            }

            // ── 5. Handle removed variant options ──
            if (!empty($validated['removed_variant_option_ids'])) {
                $optionsToDelete = VariantOptions::whereIn('variant_option_id', $validated['removed_variant_option_ids'])
                    ->whereHas('variantType', function ($q) use ($product) {
                        $q->where('product_id', $product->product_id);
                    })
                    ->get();

                foreach ($optionsToDelete as $opt) {
                    if ($opt->image && $opt->image->image_url) {
                        if (Storage::disk('s3')->exists($opt->image->image_url)) {
                            Storage::disk('s3')->delete($opt->image->image_url);
                        }
                        $opt->image->delete();
                    }
                    $opt->delete();
                }
            }

            // ── 6. Handle variant updates / additions ──
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    if (!empty($variantData['variant_type_id'])) {
                        // Existing variant type — update name if provided
                        $existingVariant = ProductVariantType::where('variant_type_id', $variantData['variant_type_id'])
                            ->where('product_id', $product->product_id)
                            ->first();

                        if ($existingVariant) {
                            if (!empty($variantData['variant_type_name'])) {
                                $existingVariant->update([
                                    'variant_name' => $variantData['variant_type_name'],
                                ]);
                            }

                            // Process options
                            if (!empty($variantData['variant_options'])) {
                                foreach ($variantData['variant_options'] as $optData) {
                                    if (!empty($optData['variant_option_id'])) {
                                        // Existing option — update changed fields
                                        $existingOption = VariantOptions::where('variant_option_id', $optData['variant_option_id'])
                                            ->where('variant_type_id', $existingVariant->variant_type_id)
                                            ->first();

                                        if ($existingOption) {
                                            $optUpdates = [];
                                            if (isset($optData['variant_option_value'])) {
                                                $optUpdates['variant_value'] = $optData['variant_option_value'];
                                            }
                                            if (isset($optData['price_adjustment'])) {
                                                $optUpdates['price_adjustment'] = $optData['price_adjustment'];
                                            }
                                            if (!empty($optUpdates)) {
                                                $existingOption->update($optUpdates);
                                            }

                                            // Handle option image change
                                            if (array_key_exists('image_url', $optData)) {
                                                if ($optData['image_url'] === null) {
                                                    // Remove existing image
                                                    if ($existingOption->image) {
                                                        if (Storage::disk('s3')->exists($existingOption->image->image_url)) {
                                                            Storage::disk('s3')->delete($existingOption->image->image_url);
                                                        }
                                                        $existingOption->image->delete();
                                                    }
                                                } else {
                                                    // Update or create image
                                                    if ($existingOption->image) {
                                                        // Delete old S3 file
                                                        if (Storage::disk('s3')->exists($existingOption->image->image_url)) {
                                                            Storage::disk('s3')->delete($existingOption->image->image_url);
                                                        }
                                                        $existingOption->image->update([
                                                            'image_url' => $optData['image_url'],
                                                        ]);
                                                    } else {
                                                        $existingOption->image()->create([
                                                            'image_url' => $optData['image_url'],
                                                            'isMain' => true,
                                                        ]);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        // New option
                                        $newOption = $existingVariant->variantOptions()->create([
                                            'variant_value' => $optData['variant_option_value'] ?? '',
                                            'price_adjustment' => $optData['price_adjustment'] ?? 0,
                                        ]);

                                        if (!empty($optData['image_url'])) {
                                            $newOption->image()->create([
                                                'image_url' => $optData['image_url'],
                                                'isMain' => true,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        // Brand new variant type
                        $newVariantType = ProductVariantType::create([
                            'product_id' => $product->product_id,
                            'variant_name' => $variantData['variant_type_name'] ?? '',
                        ]);

                        if (!empty($variantData['variant_options'])) {
                            foreach ($variantData['variant_options'] as $optData) {
                                $newOption = $newVariantType->variantOptions()->create([
                                    'variant_value' => $optData['variant_option_value'] ?? '',
                                    'price_adjustment' => $optData['price_adjustment'] ?? 0,
                                ]);

                                if (!empty($optData['image_url'])) {
                                    $newOption->image()->create([
                                        'image_url' => $optData['image_url'],
                                        'isMain' => true,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'product' => $product->fresh()->load([
                    'images',
                    'brand',
                    'category',
                    'subCategories',
                    'productTypeVariant.variantOptions.image',
                ]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($productId)
    {
        Product::where('product_id', $productId)->delete();
        return response()->json(null, 204);
    }

    public function checkSku(Request $request)
    {
        $sku = $request->query('sku');
        if (!$sku) {
            return response()->json(['exists' => false]);
        }

        $existsInProduct = Product::where('sku', $sku)->exists();
        $existsInVariant = ProductSku::where('sku_code', $sku)->exists();

        return response()->json([
            'exists' => $existsInProduct || $existsInVariant
        ]);
    }

    public function productSpecificDetail($productId)
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
            'sku',            // Simple Product SKU
            'stock_quantity'  // Simple Product Stock Quantity
        )
        ->with([
            'brand:brand_id,brand_name',
            'subCategory:sub_category_name,sub_category_id',
            'category:category_id,category_name',
            'images',
            'productTypeVariant:product_id,variant_type_id,variant_name',
            
            // 🚨 Removed price_adjustment to fix 1054 QueryException
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
            $products = $query->whereRaw(
                'LOWER(product_title) LIKE ?',
                ['%' . strtolower($productname) . '%']
            )->get();
        }

        return response()->json([
            'status' => true,
            'products' => ProductSearchResource::collection($products)
        ]);
    }

    // Dashboard Product API
    public function productListDashBoardSearch(Request $request)
    {
        $search = $request->query('productTitle');
        $brandId = $request->query('brandId');
        $categoryId = $request->query('categoryId');

        $query = Product::query()
            ->when($search, function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(product_title) LIKE ?',
                    ['%' . strtolower($search) . '%']
                );
            })
            ->when($brandId, function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->select(
                'product_id',
                'product_title',
                'base_price',
                'category_id',
                'sub_category_id',
                'brand_id',
                'sku',
                'stock_quantity'
            )
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
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
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
            'specifications'
        )
            ->with([
                'brand',
                'subCategories',
                'category',
                'images',
                'productTypeVariant',
                'productTypeVariant.variantOptions'
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
            'productdetail' => new ProductDetailsResource($productdetail)
        ]);
    }


    public function newArrivals()
    {
        $products = Product::latestArrivals()
            ->select('product_id', 'type_id', 'brand_id', 'product_name', 'base_price')
            ->with([
                'categorytype:type_id,type_name',
                'brand:brand_name,brand_id',
                'productVariants.mainImage:variant_id,url',
                'productVariants.discountsVariants:variant_id,discount_type,discount_value'
            ])
            ->limit(10)
            ->get();
        return response()->json([
            'status' => true,
            'products' => NewArrivalResource::collection($products)
        ]);
    }

    public function deleteProduct($productId)
    {
        Product::where('product_id', $productId)->delete();
        return response()->json(null, 204);
    }

    // public function ProductDetailsDashboard($productId) {
//     $productdetail = Product::select('product_id','sub_category_id','category_id','brand_id','product_title','base_price','description','features','specifications')
//                     ->with([
//                     'brand',
//                     'productMediaImage',
//                     'subCategories',
//                     'category',
//                     'productMediaImage',
//                     'productTypeVariant',
//                     'productTypeVariant.variantOptions'
//                     ])
//                     ->where('product_id', $productId)
//                     ->first();
//     if(!$productdetail) {
//         return response()->json([
//             'status' => true,
//             'productdetail' => null
//         ]);
//     }

    //     return response()->json([
//         'status' => true,
//         'productdetail' => $productdetail
//     ]);
// }














}