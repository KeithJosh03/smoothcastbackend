<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;
use App\Http\Resources\ProductDiscountCollectionResource;

class ProductDiscountController extends Controller {
        public function index() {
        $productDiscount = ProductDiscount::all();
        return response()->json([
            'status' => true,
            'productsDiscounted' => $productDiscount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductDiscount $productDiscount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductDiscount $productDiscount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductDiscount $productDiscount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductDiscount $productDiscount) {
    
    }

    public function discountedProductCollection() {
        $now = Carbon::now();
        $discountedProduct = ProductDiscount::select('discount_id', 'variant_id', 'discount_type', 'discount_value', 'endDate')
            ->with([
                'discountProductVariant:variant_id,product_id,full_model_name,product_price',
                'discountProductVariant.mainImage:variant_id,url',
                'discountProductVariant.product.brand'
            ])
            ->where('startDate', '<=', $now)
            ->where('endDate', '>=', $now)
            ->take(4)
            ->get();
        return response()->json([
            'status' => true,
            'collectioncategories' => ProductDiscountCollectionResource::collection($discountedProduct)
        ]);
    }

}
