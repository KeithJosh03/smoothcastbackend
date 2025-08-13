<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
            'description' => ['nullable','string']
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

}
