<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller {

    public function index(){
        $categories = Category::all();
        return response()->json([
            'status' => true,
            'categories' => $categories
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
            'product' => function ($query) {
                $query->select('product_id', 'category_id', 'product_name', 'base_price', 'brand_id')
                      ->take(4);
            },
            'product.brand' => function ($query) {
                $query->select('brand_id', 'brand_name');
            },
            'product.productVariant.mainImage' => function ($query) {
                $query->select('productImg_id', 'variant_id', 'url', 'isMain');
            },
        ])
        ->get();
        return response()->json($categories);
    }

    public function specificCategory($categoryname) {
        $category = Category::with(['product.brand','product.category','product.productVariant.mainImage'])
                ->where('category_name', $categoryname)
                ->first();

        if (!$category || $category->product->isEmpty()) {
            return response()->json([
                'status' => true,
                'products' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'products' => $category->product
        ]);
    }
}
