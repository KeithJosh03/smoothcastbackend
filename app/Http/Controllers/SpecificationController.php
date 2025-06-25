<?php

namespace App\Http\Controllers;

use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecificationController extends Controller {
    public function index() {
        $specification = Specification::all();
        return response()->json([
            'status' => true,
            'specifications' => $specification
        ]);
    }

    public function create() {

    }

    public function store(Request $request) {
        $validate = $request->validate([
            'specificationName' => ['required','max:100','string'],
            'variantspecification_ID' => ['required','exist:variantspecification,variantvariantspecification_ID']
        ]);

    }

    public function show(Specification $specification) {
    
    }

    public function edit(Specification $specification) {
    
    }


    public function update(Request $request, Specification $specification) {
    
    }


    public function destroy(Specification $specification) {
    
    }
}
