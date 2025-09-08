<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller {

    public function index() {
        $features = Feature::all();
        return response()->json([
            'status' => true,
            'features' => $features
        ]);
    }

    public function create() {
    
        //
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => ['required','exists:products,product_id'],
            'features' => ['required','string']
        ]);
        $specs = Feature::create($validated);
            return response()->json([
            'status' => true,
            'message' => 'Specs created successfully',
            'specs' => $specs
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature) {
    
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feature $feature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feature $feature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        //
    }
}
