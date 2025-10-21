<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller {

    public function index() {
        $variants = ProductVariant::all();
        return response()->json([
            'status' => true,
            'variants' => $variants
        ]);
    }

    public function create() {
    
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => ['nullable', 'exists:products,product_id'],
            'full_model_name' => ['required','string','max:200'],
            'product_price' => ['required','decimal:10,2']
        ]);
        $productvariant = ProductVariant::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'product' => $productvariant
        ], 201);
    }

    public function show(ProductVariant $productVariant) {
    
    }

    public function edit(ProductVariant $productVariant) {
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductVariant $productVariant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        //
    }
}
