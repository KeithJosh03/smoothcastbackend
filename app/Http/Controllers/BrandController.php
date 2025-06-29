<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{

    public function index(){
        $brands = Brand::all();
        return response()->json([
            'status' => true,
            'brands' => $brands
        ]);
    }

    public function create(){

    }

    public function store(Request $request){
        $validated = $request->validate([
        'brand_name' => ['required','string','max:100','regex:/^[a-zA-Z]+$/u'],
        ]);
        $brand = Brand::insert($validated);
        return response()->json($brand, Response::HTTP_CREATED);
    }

    public function show(Brand $brand){
        return $brand;
    }

    public function edit(Brand $brand){

    }

    public function update(Request $request, Brand $brand){
        $validated = $request->validate([
        'brand_name' => ['required','string','max:100','regex:/^[a-zA-Z]+$/u'],
        ]);
        
        $brand->update($validated);
        return response()->json($brand);
    }

    public function destroy(Brand $brand){
        $brand->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
