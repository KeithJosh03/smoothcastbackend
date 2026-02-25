<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMediaImage;
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
            'variants.*.variantOptions.*.variant_image' => ['required_with:variants.*.variantOptions', 'string'],
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
                            $variantOption->images()->create([
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

        }
        catch (\Exception $e) {

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
            'brand_id' => ['sometimes', 'nullable', 'exists:brands,brand_id'],
            'category_id' => ['sometimes', 'exists:categories,category_id'],
            'sub_category_id' => ['sometimes', 'nullable', 'exists:sub_categories,sub_category_id'],
            'product_title' => ['sometimes', 'string', 'max:100'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            'features' => ['sometimes', 'nullable', 'string'],
            'specifications' => ['sometimes', 'nullable', 'string'],
        ]);

        $product->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'product' => $product->fresh()
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
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
            'productTypeVariant.variantOptions:variant_type_id,variant_option_id,image_url,variant_option_value,price_adjustment'
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
            // 'productdetail' => $productdetail
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
            'productThumbNail:product_id,url',
            'productTypeVariant:product_id,variant_type_id,variant_type_name',
            'productTypeVariant.variantOptionsFirstImage:variant_type_id,image_url',
            'productTypeVariant.variantOptions:variant_type_id,price_adjustment,variant_option_value'
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
        $productdetail = Product::select('product_id', 'sub_category_id', 'category_id', 'brand_id', 'product_title', 'base_price', 'description', 'features', 'specifications')
            ->with([
            'brand',
            'productMediaImage',
            'subCategories',
            'category',
            'productMediaImage',
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
            // 'productdetail' => $productdetail
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