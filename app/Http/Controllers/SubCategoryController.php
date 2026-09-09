<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller {

    public function index(){
        $categorytype = SubCategory::orderBy('sort_order', 'asc')->orderBy('sub_category_id', 'asc')->get();
        return response()->json([
            'status' => true,
            'categorytypes' => SubCategoryResource::collection($categorytype)
        ]);
    }

    public function create() {
    
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,category_id'],
            'sub_category_name' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer']
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
            'sub_category_name' => ['sometimes', 'required', 'string', 'max:100'],
            'category_id' => ['sometimes', 'required', 'exists:categories,category_id'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer']
        ]);

        $subcategory->update($validated);
        return response()->json(new SubCategoryResource($subcategory));
    }

    public function destroy(SubCategory $subcategory) {
        $subcategory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function toggleStatus(Request $request, $id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean']
        ]);

        $newStatus = array_key_exists('is_active', $validated) ? $validated['is_active'] : !$subcategory->is_active;
        $subcategory->update(['is_active' => $newStatus]);

        return response()->json(new SubCategoryResource($subcategory));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required'],
            'orders.*.sort_order' => ['required', 'integer']
        ]);

        foreach ($validated['orders'] as $item) {
            SubCategory::where('sub_category_id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['status' => true, 'message' => 'Subcategories reordered successfully']);
    }
}
