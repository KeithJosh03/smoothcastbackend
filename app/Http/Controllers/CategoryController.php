<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\CategoryCollectionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\CategorySubResource;
use App\Http\Resources\SpecificCategoryProductResource;


class CategoryController extends Controller {

    public function index(){
        $categories = Category::all();
        return response()->json([
            'status' => true,
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    public function create(){
    
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:100']
        ]);

        $category = Category::create([
            'category_name' => $validated['category_name']
        ]);
        return response()->json(new CategoryResource($category), Response::HTTP_CREATED);
    }

    public function show(Category $category){
        return $category;
    }

    public function edit(Category $category){
    }

    public function update(Request $request, Category $category){
        $validated = $request->validate([
            'category_name' => ['required','string','max:100'],
        ]);

        $category->update($validated);

        return response()->json(new CategoryResource($category));
    }

    public function destroy(Category $category){
        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function categoryproductcollection() {
        $categories = Category::select('category_id', 'category_name')


            ->with([
                'products' => function ($query) {
                $query->select('product_id', 'product_title', 'category_id', 'brand_id', 'base_price', 'sub_category_id')
                    ->with([
                        'brand:brand_id,brand_name',
                        'subCategories:category_id,sub_category_id,sub_category_name',
                        'productThumbNail:url,product_img_id,product_id',
                        'productTypeVariant.variantOptionsFirstImage'
                    ])->take(4);
                }
            ])
            ->get(); 

        return response()->json([
            'categories' => new CategoryCollectionResource($categories)
            // 'categories' => $categories
        ]);
    }




    public function specificCategory($categoryname, Request $request) {
        $perPage = 15; 
        $page = $request->get('page', 1);
        
        $category = Category::where('category_name', $categoryname)->first();
        
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        $products = $category
        ->products()
        ->select('base_price','product_id','brand_id','sub_category_id','product_title')
        ->with([
            'brand:brand_id,brand_name',
            'subCategories:sub_category_id,sub_category_name',
            'productThumbNail:product_img_id,product_id,url',
            'productTypeVariantFirst.variantOptionsFirstImage:variant_option_id,variant_type_id,image_url'
        ])
        ->paginate($perPage, ['*'], 'page', $page);

        if ($products->isEmpty()) {
            return response()->json([
                'status' => true,
                'categoryproducts' => [
                    'categoryId' => $category->category_id,
                    'categoryName' => $category->category_name,
                    'products' => []
                ],
                'currentPage' => 1,
                'lastPage' => 1,
                'hasMore' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'categoryproducts' => [
                'categoryId' => $category->category_id,
                'categoryName' => $category->category_name,
                'products' => SpecificCategoryProductResource::collection($products->items())
            ],
            'currentPage' => $products->currentPage(),
            'lastPage' => $products->lastPage(),
            'hasMore' => $products->hasMorePages()
        ]);
    }

    public function categorySub($categoryId) {
        $category = Category::where('category_id', $categoryId)
                            ->with('subCategories:category_id,sub_category_id,sub_category_name')
                            ->first();

        if(!$category) {
            return response()->json([
                'status' => true,
                'category' => null 
            ]);
        }

        return response()->json([
            'status' => true,
            'categorySubs' => CategorySubResource::collection($category->subCategories) 
            // 'categorySub' => $category

        ]);
    }


    public function subCatByCategoryId($categoryId) {
        $category = Category::where('category_id', $categoryId)
                            ->with('subcategories:category_id,sub_category_id,sub_category_name')
                            ->first();

        if(!$category) {
            return response()->json([
                'status' => true,
                'category' => null 
            ]);
        }

        return response()->json([
            'status' => true,
            // 'categorySub' => new CategorySubResource($category) 
            'categorySub' =>  SubCategoryResource::collection($category->subcategories)
        ]);
    }
}



