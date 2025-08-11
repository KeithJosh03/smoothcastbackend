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

    public function getApparel(){
        $apparelcategory = Category::where('category_name', 'Apparel')->get();
        if ($apparelcategory->isEmpty()) {
            return response()->json([
                'status' => true,
                'categories' => []
            ]);
        }
        return response()->json([
            'status' => true,
            'categories' => $apparelcategory
        ]);
    }

    public function categoryproduct (){
        $categories = Category::with('product')->get();

        return response()->json($categories);
    }
}
