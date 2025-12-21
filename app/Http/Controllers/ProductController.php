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
use App\Http\Resources\NewArrivalResource;

class ProductController extends Controller {
    public function index(){
        // $products = Product::paginate(15);
        $products = Product::get();
        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }

    public function create(){
    
    }

    public function store(Request $request) {
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
            'medias.*.isMain' => ['required', 'boolean'],  
            'medias.*.url' => ['required', 'string'],

            'variants' => ['nullable', 'array'],
            'variants.*.variantTypeName' => ['required_with:variants', 'string'],
            'variants.*.variantOptions' => ['required_with:variants', 'array'],

            'variants.*.variantOptions.*.price_adjustment' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'variants.*.variantOptions.*.variantOptionValue' => [
                'required_with:variants.*.variantOptions',
                'string'
            ],

            'variants.*.variantOptions.*.variant_image' => [
                'required_with:variants.*.variantOptions',
                'string'
            ],
        ]);

        // Step 2: Start a database transaction
        DB::beginTransaction();
        try {
            // Step 3: Create the product
            $product = Product::create([
                'brand_id' => $validated['brand_id'] ?? null,
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'product_title' => $validated['product_title'],
                'base_price' => $validated['base_price'],
                'description' => $validated['description'] ?: null,
                'features' => $validated['features'] ?: null,
                'specifications' => $validated['specifications'] ?: null,
                'release_date' => now(),
            ]);

            // Step 4: Handle media (product images)
            if (!empty($validated['medias'])) {
                foreach ($validated['medias'] as $media) {
                    // Store the media URL and whether it's the main image
                    ProductMediaImage::create([
                        'product_id' => $product->product_id,
                        'url' => $media['url'],
                        'isMain' => $media['isMain'],
                    ]);
                }
            }

            // Step 5: Handle product variants (optional)
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variant) {

                    // Create Variant Type
                    $variantType = ProductVariantType::create([
                        'product_id' => $product->product_id,
                        'variant_type_name' => $variant['variantTypeName'],
                    ]);

                    // Create Variant Options
                    foreach ($variant['variantOptions'] as $option) {
                        $variantType->variantOptions()->create([
                            'variant_option_value' => $option['variantOptionValue'],
                            'price_adjustment' => $option['price_adjustment'],
                            'image_url' => $option['variant_image'],
                        ]);
                    }
                }
            }

            // Step 6: Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'product' => $product,
            ], 201);
        } catch (\Exception $e) {
            // Step 7: Rollback if anything fails
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }





    public function show(Product $product){
        dd($product);
        return $product;
    }

    public function edit(Product $product){
    
    }

    public function update(Request $request, Product $product){
    
    }

    public function destroy(Product $product){
    
    }

    public function productSpecificDetail ($productId) {
        $productdetail = Product::select('product_id','product_name','base_price','description','features','specifications','sub_category_id', 'brand_id')
                        ->with([
                        'brand:brand_id,brand_name',
                        'productVariants:product_id,variant_id,variant_name,variant_price',
                        'productVariants.allImage:product_img_id,variant_id,url,isMain',
                        'subcategory:sub_category_id,sub_category_name',
                        'productVariants.discountsVariants:variant_id,endDate,discount_type,discount_value'
                        ])
                        ->where('product_id', $productId)
                        ->first();
        if(!$productdetail) {
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

    public function productDetailInitial ($productId) {
        $productdetail = Product::select('product_id','product_name')
                        ->with([
                        'firstVariant:product_id,variant_id,variant_price'
                        ])
                        ->where('product_id', $productId)
                        ->first();
        if(!$productdetail) {
            return response()->json([
                'status' => true,
                'productdetail' => null
            ]);
        }

    return response()->json([
            'status' => true,
            'productdetail' => new ProductDetailInitialResource($productdetail)
        ]);
    }



    public function productSearch ($productname) {
        $products = Product::where('product_name','LIKE','%'. $productname . '%')
                    ->select('product_id','product_name')
                    ->get();
        if($products->isEmpty()){
            return response()->json([
            'status' => true,
            'products' => []
            ]);
        }
        return response()->json([
            'status' => true,
            'products' => ProductSearchResource::collection($products)
        ]);

    }
    
    public function newArrivals() {
        $products = Product::latestArrivals()
        ->select('product_id','type_id','brand_id','product_name','base_price')
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
}
