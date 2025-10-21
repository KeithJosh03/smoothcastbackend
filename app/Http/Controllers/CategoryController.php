<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\CategoryCollectionResource;
use App\Http\Resources\CategoryResource;
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

    public function store(Request $request){
        $validate = $request->validate([
            'category_name' => ['required','string','max:100']
        ]);
        $categories = Category::insert($validate);
        return response()->json($categories, Response::HTTP_CREATED);
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
        return response()->json($category);
    }

    public function destroy(Category $category){
        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function categoryproductcollection () {
        $categories = Category::select('category_id', 'category_name')
        ->with([
            'products' => function ($query) {
                $query->select('product_id','product_name','category_id','brand_id','base_price','type_id')
                    ->with([
                        'brand:brand_id,brand_name',
                        'productVariants.product.categorytype',
                        'productVariants' => function ($q) {
                            $q->select('variant_id','product_id','full_model_name','product_price')
                                ->with([
                                    'mainImage:product_img_id,variant_id,url,isMain',
                                    'discountsVariants:variant_id,discount_type,discount_value'
                                ]);
                        }
                ])->take(4);
            }
        ])
        ->get();

        return response()->json([
            'status' => true,
            'collectioncategories' => new CategoryCollectionResource($categories)
        ]);
    }

    public function specificCategory($categoryname, Request $request) {
        $perPage = 12; 
        $page = $request->get('page', 1);
        
        $category = Category::where('category_name', $categoryname)->first();
        
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        $products = $category->products()
            ->with([
                'brand:brand_name,brand_id',
                'categorytype:type_id,type_name',
                'productVariants:variant_id,product_id,full_model_name,product_price',
                'productVariants.mainImage:variant_id,url',
                'productVariants.discountsVariants:variant_id,discount_type,discount_value'
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
}



