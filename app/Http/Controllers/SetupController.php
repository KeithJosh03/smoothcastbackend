<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Setup;
use Illuminate\Http\Request;
use App\Http\Resources\SetupDetailsResource;
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

    public function store(Request $request) {
    
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setup $setup)
    {

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
        'mainImageSetup:setup_id,url',
        'package.packagevariant:variant_id,product_price,product_id,full_model_name',
        'package.packagevariant.product:product_id,category_id,type_id,product_name',
        'package.packagevariant.product.categorytype:type_id,type_name'
        ])
        ->get();

        return response()->json([
            'status' => true,
            'setupcollection' => SetupCollectionResource::collection($setupcollection)
        ]);
    }


    public function specificSetup($setupId) {
        $setupDetail = Setup::select('setup_id','setup_name','code_name','description','start_date','end_date','value_discount','type_discount')
            ->with([
            'setupimages:setup_id,url,isMain,setup_img_id',
            'package:variant_id,package_id,setup_id',
            'package.packagevariant:variant_id,full_model_name,product_id,product_price',
            'package.packagevariant.product:product_id,type_id',
            'package.packagevariant.product.categorytype:type_id,type_name'
            ])
            ->where('setup_id', $setupId)
            ->first();

        if (!$setupDetail || $setupDetail->package->isEmpty()) {
            return response()->json([
                'status' => true,
                'setupDetail' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'setupDetail' => new SetupDetailsResource($setupDetail) 
        ]);
    }

    public function setups(Request $request) {
        $now = Carbon::now();
        $perPage = 12;
        $page = $request->get('page', 1);

        $setups = Setup::select('setup_id', 'setup_name', 'value_discount', 'type_discount', 'code_name')
            ->with([
                'package:variant_id,package_id,setup_id',
                'mainImageSetup:setup_id,url',
                'package.packagevariant:variant_id,product_price,product_id,full_model_name',
                'package.packagevariant.product:product_id,category_id,type_id,product_name',
                'package.packagevariant.product.categorytype:type_id,type_name'
            ])
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'setups' => SetupCollectionResource::collection($setups->items()),
            'currentPage' => $setups->currentPage(),
            'lastPage' => $setups->lastPage(),
            'hasMore' => $setups->hasMorePages()
        ]);
    }

}
