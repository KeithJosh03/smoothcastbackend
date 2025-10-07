<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\BrandSpecificProducts;
use App\Http\Resources\BrandResource;


class BrandController extends Controller {

    public function index(){
        $brands = Brand::all();
        return response()->json([
            'status' => true,
            'brands' => BrandResource::collection($brands)
        ]);
    }

    public function create(){

    }

    public function store(Request $request){
        $validated = $request->validate([
        'brand_name' => ['required','string','max:100'],
        'image_url' => ['nullable','string','max:100']
        ]);
        $brand = Brand::insert($validated);
        return response()->json($brand, Response::HTTP_CREATED);
    }

    public function show(Brand $brand){
        return $brand;
    }

    public function edit(Brand $brand){

    }

    public function update(Request $request, Brand $brand){
        $validated = $request->validate([
        'brand_name' => ['required','string','max:100'],
        ]);
        
        $brand->update($validated);
        return response()->json($brand);
    }

    public function destroy(Brand $brand){
        $brand->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function specificbrand($brandname) {
        $brandproducts = Brand::where('brand_name', $brandname)
            ->with(['brandProducts.categorytype','brandProducts.productVariant.mainImage']) 
            ->first(); 

        return response()->json([
            'status' => true,
            'brandImage' => $brandproducts->image_url,
            'products' => BrandSpecificProducts::collection($brandproducts->brandProducts)
        ]);
    }



    public function brandLogo () {
        $brand = Brand::whereNotNull('image_url')->get();
        if (!$brand || $brand->isEmpty()) {
            return response()->json([
                'status' => true,
                'brandLogo' => []
            ]);
        }
        return response()->json([
            'status' => true,
            'brandlogo' => BrandResource::collection($brand)
        ]);
    }

    public function headerBrand(){
        $brands = Brand::all(['brand_id','brand_name']);
        return response()->json([
            'status' => true,
            'brands' => BrandResource::collection($brands)
        ]);
    }

}
