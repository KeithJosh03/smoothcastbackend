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
                        'productVariant.product.categorytype',
                        'productVariant' => function ($q) {
                            $q->select('variant_id','product_id','full_model_name','product_price')
                                ->with([
                                    'mainImage:product_img_id,variant_id,url,isMain'
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

    public function specificCategory($categoryname) {
        $category = Category::with([
            'products:product_id,category_id,type_id,brand_id,product_name,base_price',
            'products.brand:brand_name,brand_id',
            'products.categorytype:type_id,type_name',
            'products.productVariant.mainImage'
            ])
            ->where('category_name', $categoryname)
            ->first();

        if (!$category || $category->products->isEmpty()) {
            return response()->json([
                'status' => true,
                'categoryproducts' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'categoryproducts' => new SpecificCategoryProductResource($category) 
        ]);
    }
}
