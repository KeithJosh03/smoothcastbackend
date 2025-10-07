<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductSearchResource;
use App\Http\Resources\ProductDetailInitialResource;


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
            'type_id' => ['nullable', 'exists:category_types,type_id'],
            'product_name' => ['required','string','max:100'],
            'base_price' => ['required','decimal:10,2'],
            'description' => ['nullable','string'],
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
                        'specification:product_id,specification',
                        'features:product_id,features',
                        'productVariant:product_id,variant_id,full_model_name,product_price',
                        'productVariant.allImage:product_img_id,variant_id,url,isMain',
                        'categorytype:type_id,type_name'
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
        $products = Product::newArrivals()->get();

        return response()->json([
            'status' => true,
            'products' => $products
        ]);
    }
}
