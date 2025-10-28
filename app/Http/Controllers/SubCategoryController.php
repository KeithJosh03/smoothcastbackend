<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller {

    public function index(){
        $categorytype = SubCategory::all();
        return response()->json([
            'status' => true,
            'categorytypes' => $categorytype
        ]);
    }

    public function create() {
    
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,category_id'],
            'sub_category_name' => ['required', 'string', 'max:100']
        ]);
        $subcategory = SubCategory::create($validated);
        return response()->json(new SubCategoryResource($subcategory), Response::HTTP_CREATED);
    }

    public function show(SubCategory $categoryType) {

    }

    public function edit(SubCategory $categoryType) {
    
    }

    public function update(Request $request, SubCategory $subcategory) {
        $validated = $request->validate([
            'sub_category_name' => ['required', 'string', 'max:100']
        ]);

        $subcategory->update($validated);
         return response()->json(new SubCategoryResource($subcategory));
    }

    public function destroy(SubCategory $subcategory) {
        $subcategory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
