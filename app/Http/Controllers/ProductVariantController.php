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
    
    }

    public function show(ProductVariant $productVariant) {
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $productVariant)
    {
        //
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
