<?php

namespace App\Http\Controllers;

use App\Models\Setup;
use Illuminate\Http\Request;
use App\Http\Resources\SetupResource;
use App\Http\Resources\SetupCollectionResource;

class SetupController extends Controller {

    public function index() {
        $setups = Setup::all();
        return response()->json([
            'status' => true,
            'setups' => SetupResource::collection($setups)
        ]);

    }

    public function create() {
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
    
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setup $setup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setup $setup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setup $setup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setup $setup)
    {
        //
    }

    public function setupcollection () {
        $setupcollection = Setup::select('setup_id','setup_name','value_discount','type_discount','code_name')
        ->with([
        'package:variant_id,package_id,setup_id',
        'inclusionSetup:setup_id,inclusion',
        'mainImageSetup:setup_id,url',
        'package.packagevariant:variant_id,product_id,full_model_name',
        'package.packagevariant.product:product_id,type_id,product_name',
        'package.packagevariant.product.categorytype:type_id,type_name'
        ])
        ->get();

        return response()->json([
            'status' => true,
            'setupcollection' => SetupCollectionResource::collection($setupcollection)
        ]);
    }
}
