<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Http\Resources\BrandSpecificProducts;
use App\Http\Resources\BrandSpecificProductsCollection;
use App\Http\Resources\BrandResource;


class BrandController extends Controller
{

    public function index()
    {
        $brands = Brand::all();
        return response()->json([
            'status' => true,
            'brands' => BrandResource::collection($brands)
        ]);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'string'],
        ]);

        $brand = Brand::create([
            'brand_name' => $validated['brand_name'],
        ]);

        $brand->image()->create([
            'image_url' => $validated['image_url'],
            'isMain' => true,
        ]);

        $brand->load('image');

        return response()->json(
            new BrandResource($brand),
            Response::HTTP_CREATED
        );
    }

    public function show(Brand $brand)
    {
        return $brand;
    }

    public function edit(Brand $brand)
    {

    }

    public function update(Request $request, Brand $brand)
    {
        // Validate the incoming request for brand name and image URL
        $validated = $request->validate([
            'brand_name' => ['sometimes', 'string', 'max:100'],
            'image_url' => ['sometimes', 'string'], // image_url will come from the upload response
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
                    'isMain' => true, // Assuming this is the main image
                ]);
            }
            else {
                // If no image exists, create a new image record for the brand
                $brand->image()->create([
                    'image_url' => $validated['image_url'],
                    'isMain' => true,
                ]);
            }
        }

        // Save the brand if it's been modified (though typically it's already done with the update and image handling)
        $brand->save();

        return response()->json(new BrandResource($brand));
    }




    public function destroy(Brand $brand)
    {
        if ($brand->brandProducts()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete brand with linked products.'
            ], 422);
        }

        $brand->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function specificBrand($brandname, Request $request)
    {
        $perPage = 12; // Products per page
        $page = $request->get('page', 1);

        $brand = Brand::where('brand_name', $brandname)->first();

        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ], 404);
        }

        $search = $request->get('search');
        $budget = $request->get('budget');
        $sort = $request->get('sort', 'newest');

        $query = $brand->brandProducts()
            ->select(
                'products.product_id',
                'products.product_title',
                'products.base_price',
                'products.brand_id',
                'products.sub_category_id',
                'products.category_id'
            )
            ->selectSub(function ($q) {
                $q->from('product_skus')
                    ->whereColumn('product_skus.product_id', 'products.product_id')
                    ->selectRaw('MIN(price)');
            }, 'min_variant_price')
            ->selectSub(function ($q) {
                $q->from('product_skus')
                    ->whereColumn('product_skus.product_id', 'products.product_id')
                    ->selectRaw('MAX(price)');
            }, 'max_variant_price')
            ->with([
                'category:category_id,category_name',
                'subCategory:sub_category_id,sub_category_name',
                // Direct product media / main image
                'mainImage:image_id,imageable_id,imageable_type,image_url,isMain',
                // Fallback 1: SKU-level images
                'firstProductSku.mainImage:image_id,imageable_id,imageable_type,image_url,isMain',
                // Fallback 2: Variant options / attributes level images
                'productTypeVariantFirst.firstVariantOption.image',
            ]);

        if (!empty($search)) {
            $query->where('products.product_title', 'like', "%{$search}%");
        }

        if (!empty($budget) && is_numeric($budget)) {
            $query->where('products.base_price', '<=', $budget);
        }

        switch ($sort) {
            case 'price_low':
                $query->orderBy('products.base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('products.base_price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('products.product_id', 'desc');
                break;
        }

        $brandproducts = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'brandImage' => $brand->image_url,
            'brandId' => $brand->brand_id,
            'brandName' => $brand->brand_name,
            'products' => new BrandSpecificProductsCollection($brandproducts),
            'currentPage' => $brandproducts->currentPage(),
            'lastPage' => $brandproducts->lastPage(),
            'hasMore' => $brandproducts->hasMorePages()
        ]);
    }



    public function brandLogo()
    {
        $brands = Brand::with('image') 
            ->withCount('brandProducts')
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

    public function BrandNameListSearchHeader()
    {
        $brands = \Cache::remember('header_brands_list', 86400, function () {
            return Brand::get(['brand_id', 'brand_name']);
        });
        return response()->json([
            'status' => true,
            'data' => BrandResource::collection($brands)
        ]);
    }

}