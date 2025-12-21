<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function store(Request $request) {
        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'string'],
        ]);

        $brand = Brand::create($validated);

        return response()->json([
            'brandId' => $brand->brand_id,
            'brandName' => $brand->brand_name, 
            'imageUrl' => $brand->image_url,
        ], Response::HTTP_CREATED);
    }

    public function show(Brand $brand){
        return $brand;
    }

    public function edit(Brand $brand){

    }

    public function update(Request $request, Brand $brand) {
        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:100'],
            'image_url' => ['required','string'],
        ]);


        $brand->update($validated);
        return response()->json(new BrandResource($brand));
    }




    public function destroy(Brand $brand){
        $brand->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function specificbrand($brandname, Request $request) {
        $perPage = 12; // Products per page
        $page = $request->get('page', 1);
        
        $brand = Brand::where('brand_name', $brandname)->first();
        
        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ], 404);
        }
        
        $brandproducts = $brand->brandProducts()
            ->with([
                'categorytype:type_name,type_id',
                'productVariants:product_id,variant_id,full_model_name,product_price',
                'productVariants.discountsVariants:variant_id,discount_type,discount_value',
                'productVariants.mainImage:variant_id,url',
            ])
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'brandImage' => $brand->image_url,
            'brandId' => $brand->brand_id,
            'products' => BrandSpecificProducts::collection($brandproducts->items()),
            'currentPage' => $brandproducts->currentPage(),
            'lastPage' => $brandproducts->lastPage(),
            'hasMore' => $brandproducts->hasMorePages()
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
