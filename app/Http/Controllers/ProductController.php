<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

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
            'brand_ID' => ['nullable', 'exists:brands,brand_ID'],
            'category_ID' => ['required', 'exists:categories,category_ID'],
            'productName' => ['required','string','max:100'],
            'price' => ['required','numeric'],
            'description' => ['nullable','string','max:255']
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
