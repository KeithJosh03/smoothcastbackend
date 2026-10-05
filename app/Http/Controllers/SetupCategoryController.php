<?php

namespace App\Http\Controllers;

use App\Models\SetupCategory;
use Illuminate\Http\Request;

class SetupCategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => SetupCategory::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:setup_categories,slug'
        ]);
        
        $category = SetupCategory::create($validated);
        
        return response()->json([
            'status' => true,
            'message' => 'Setup category created',
            'data' => $category
        ]);
    }

    public function show($id)
    {
        $category = SetupCategory::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = SetupCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:setup_categories,slug,' . $id
        ]);
        
        $category->update($validated);
        
        return response()->json([
            'status' => true,
            'message' => 'Setup category updated',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = SetupCategory::findOrFail($id);
        $category->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Setup category deleted'
        ]);
    }
}
