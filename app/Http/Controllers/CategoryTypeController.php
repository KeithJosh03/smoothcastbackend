<?php

namespace App\Http\Controllers;

use App\Models\CategoryType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryTypeController extends Controller {

    public function index(){
        $categorytype = CategoryType::all();
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
            'type_name' => ['required', 'string', 'max:100']
        ]);
        $type = CategoryType::create($validated);
        return response()->json([
        'status' => true,
        'categorytype' => $type
        ], Response::HTTP_CREATED);
    }

    public function show(CategoryType $categoryType) {

    }

    public function edit(CategoryType $categoryType) {
    
    }

    public function update(Request $request, CategoryType $categoryType) {
        $validated = $request->validate([
        'type_name' => ['required', 'string', 'max:100'],
        ]);
        $categoryType->update($validated);

        return response()->json($categoryType);
    }

    public function destroy(CategoryType $categoryType) {
        $categoryType->forceDelete();
        return response()->json([
            'status' => true,
            'message' => 'CategoryType deleted successfully.'
        ],Response::HTTP_OK);
    }
}
