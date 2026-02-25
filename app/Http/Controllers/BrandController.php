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
            'image_url'  => ['required', 'string'],
        ]);

        $brand = Brand::create([
            'brand_name' => $validated['brand_name'],
        ]);

        $brand->image()->create([
            'image_url' => $validated['image_url'],
            'isMain'    => true,
        ]);

        $brand->load('image');

        return response()->json(
            new BrandResource($brand),
            Response::HTTP_CREATED
        );
    }

    public function show(Brand $brand){
        return $brand;
    }

    public function edit(Brand $brand){

    }

    public function update(Request $request, Brand $brand) {
        // Validate the incoming request for brand name and image URL
        $validated = $request->validate([
            'brand_name' => ['sometimes', 'string', 'max:100'],
            'image_url'  => ['sometimes', 'string'], // image_url will come from the upload response
        ]);

        // Update brand name if provided
        if (isset($validated['brand_name'])) {
            $brand->brand_name = $validated['brand_name'];
        }

        // If an image URL is provided, update or create the brand's image record
        if (isset($validated['image_url'])) {
            $brandImage = $brand->image;

            if ($brandImage) {
                // If the brand already has an image, update it
                $brandImage->update([
                    'image_url' => $validated['image_url'],
                    'isMain'    => true, // Assuming this is the main image
                ]);
            } else {
                // If no image exists, create a new image record for the brand
                $brand->image()->create([
                    'image_url' => $validated['image_url'],
                    'isMain'    => true,
                ]);
            }
        }

        // Save the brand if it's been modified (though typically it's already done with the update and image handling)
        $brand->save();

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



    public function brandLogo() {
        $brands = Brand::with('image') // eager load image
            ->whereHas('image', function ($query) {
                $query->whereNotNull('image_url');
            })
            ->get();

        if ($brands->isEmpty()) {
            return response()->json([
                'status' => true,
                'brandLogo' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'brandLogo' => BrandResource::collection($brands)
        ]);
    }

    public function BrandNameList() {
        $brands = Brand::with('image')
            ->get(['brand_id', 'brand_name']);
        return response()->json([
            'status' => true,
            'brands' => BrandResource::collection($brands)
        ]);
    }

}
