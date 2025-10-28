<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductSearchResource;
use App\Http\Resources\ProductDetailInitialResource;
use App\Http\Resources\NewArrivalResource;

class ProductController extends Controller {
    public function index(){
        $products = Product::all();
        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }

    public function create(){
    
    }

    public function store(Request $request){
        $validated = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,brand_id'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'sub_category_id' => ['nullable', 'exists:sub_category,sub_category_id'],
            'product_name' => ['required','string','max:100'],
            'base_price' => ['required','decimal:10,2'],
            'description' => ['nullable','string'],
            'features' => ['required','string'],
            'specifications' => ['required','string'],
            'release' => ['nullable', 'date'],
        ]);
        $product = Product::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function show(Product $product){
        return $product;
    }

    public function edit(Product $product){
    
    }

    public function update(Request $request, Product $product){
    
    }

    public function destroy(Product $product){
    
    }

    public function productSpecificDetail ($productId) {
        $productdetail = Product::select('product_id','product_name','base_price','description', 'type_id', 'brand_id')
                        ->with([
                        'brand:brand_id,brand_name',
                        'productVariants:product_id,variant_id,full_model_name,product_price',
                        'productVariants.allImage:product_img_id,variant_id,url,isMain',
                        'categorytype:type_id,type_name',
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
                        'firstVariant:product_id,variant_id,product_price'
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
