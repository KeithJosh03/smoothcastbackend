<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariantType;
use App\Models\VariantOptions;


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
        $validated = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,brand_id'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,sub_category_id'],
            'product_title' => ['required', 'string', 'max:100'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],

            'medias' => ['nullable', 'array'],
            'medias.*.isMain' => ['nullable', 'boolean'],
            'medias.*.url' => ['required_with:medias', 'string'],

            'variants' => ['nullable', 'array'],
            'variants.*.variantTypeName' => ['required_with:variants', 'string'],
            'variants.*.variantOptions' => ['required_with:variants', 'array'],
            'variants.*.variantOptions.*.price_adjustment' => ['nullable', 'numeric', 'min:0'],
            'variants.*.variantOptions.*.variantOptionValue' => ['required_with:variants.*.variantOptions', 'string'],
            'variants.*.variantOptions.*.variant_image' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        // Create Product
        try {
            $product = Product::create([
                'brand_id' => $validated['brand_id'] ?? null,
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'product_title' => $validated['product_title'],
                'base_price' => $validated['base_price'],
                'description' => $validated['description'] ?? null,
                'features' => $validated['features'] ?? null,
                'specifications' => $validated['specifications'] ?? null,
                'release_date' => now(),
            ]);

            // Store product iamges
            if (!empty($validated['medias'])) {
                $mainCount = collect($validated['medias'])->where('isMain', true)->count();
                if ($mainCount > 1) {
                    throw new \Exception('Only one main image is allowed.');
                }

                $hasMain = collect($validated['medias'])->contains('isMain', true);

                foreach ($validated['medias'] as $index => $media) {
                    $product->images()->create([
                        'image_url' => $media['url'],
                        'isMain' => $hasMain
                            ? ($media['isMain'] ?? false)
                            : $index === 0, // if no main, first image becomes main
                    ]);
                }
            }



            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variant) {
                    $variantType = ProductVariantType::create([
                        'product_id' => $product->product_id,
                        'variant_type_name' => $variant['variantTypeName'],
                    ]);

                    foreach ($variant['variantOptions'] as $option) {
                        $variantOption = $variantType->variantOptions()->create([
                            'variant_option_value' => $option['variantOptionValue'],
                            'price_adjustment' => $option['price_adjustment'] ?? 0,
                        ]);

                        // 3a️⃣ Store variant option image (optional)
                        if (!empty($option['variant_image'])) {
                            $variantOption->image()->create([
                                'image_url' => $option['variant_image'],
                                'isMain' => true,
                            ]);
                        }
                    }
                }
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'product' => $product->load('images'),
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
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
                                    'variant_type_name' => $variantData['variant_type_name'],
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
                                                $optUpdates['variant_option_value'] = $optData['variant_option_value'];
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
                                            'variant_option_value' => $optData['variant_option_value'] ?? '',
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
                            'variant_type_name' => $variantData['variant_type_name'] ?? '',
                        ]);

                        if (!empty($variantData['variant_options'])) {
                            foreach ($variantData['variant_options'] as $optData) {
                                $newOption = $newVariantType->variantOptions()->create([
                                    'variant_option_value' => $optData['variant_option_value'] ?? '',
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

    public function productSpecificDetail($productId)
    {
        $productdetail = Product::select('product_id', 'sub_category_id', 'category_id', 'brand_id', 'product_title', 'base_price', 'description', 'features', 'specifications')
            ->with([
                'brand:brand_id,brand_name',
                'subCategories:sub_category_name,sub_category_id',
                'category:category_id,category_name',
                'images',
                'productTypeVariant:product_id,variant_type_id,variant_type_name',
                'productTypeVariant.variantOptions:variant_type_id,variant_option_id,variant_option_value,price_adjustment',
                'productTypeVariant.variantOptions.image'
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
        $productname = $request->query('productTitle');
        $products = Product::whereRaw(
            'LOWER(product_title) LIKE ?',
            ['%' . strtolower($productname) . '%']
        )
            ->select('product_id', 'product_title')
            ->get();

        return response()->json([
            'status' => true,
            'products' => ProductSearchResource::collection($products)
        ]);
    }

    // Dashboard Product API
    public function productListDashBoardSearch(Request $request)
    {

        $search = $request->query('productTitle');

        $query = Product::query()
            ->when($search, function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(product_title) LIKE ?',
                    ['%' . strtolower($search) . '%']
                );
            })
            ->select(
                'product_id',
                'product_title',
                'base_price',
                'category_id',
                'sub_category_id',
                'brand_id'
            )
            ->with([
                'category:category_id,category_name',
                'brand:brand_id,brand_name',
                'subCategories:sub_category_id,sub_category_name',
                'mainImage:image_id,imageable_id,imageable_type,image_url,isMain',
                'productTypeVariant:product_id,variant_type_id,variant_type_name',
                'productTypeVariant.variantOptions:variant_option_id,variant_type_id,price_adjustment,variant_option_value',
                'productTypeVariant.variantOptions.image:image_id,imageable_id,imageable_type,image_url,isMain'
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