<?php

namespace App\Http\Controllers;

use App\Models\ProductMediaImage;
use Illuminate\Http\Request;

class PProductMediaImageController extends Controller {

    public function index() {
        $productimage = ProductImage::all();
        return response()->json([
            'status' => true,
            'images' => $productimage
        ]);
    }

    public function create() {
    
    }

    public function store(Request $request) {
    
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductImage $productImage)
    {
        //
    }
}
