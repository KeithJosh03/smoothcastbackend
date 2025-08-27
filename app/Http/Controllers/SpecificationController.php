<?php

namespace App\Http\Controllers;

use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationController extends Controller {

    public function index() {
        $specs = Specification::all();
        return response()->json([
            'status' => true,
            'specs' => $specs
        ]);
    }

    public function create() {
    
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'variant_id' => ['required','exists:product_variants,variant_id'],
            'specification' => ['required','string','max:500']
        ]);

        $specs = Specification::create($validated);
            return response()->json([
            'status' => true,
            'message' => 'Specs created successfully',
            'specs' => $specs
        ], 201);
    }

    public function show(Specification $specification) {
    
    }

    public function edit(Specification $specification) {
    
    }

    public function update(Request $request, Specification $specification) {
    
    }

    public function destroy(Specification $specification)
    {
        //
    }
}
