<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryTypeController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\ProductVariantController;


Route::get('/categories/getapparel', [CategoryController::class, 'getApparel']);
Route::get('/categories/specificCategory/{categoryname}', [CategoryController::class, 'specificCategory']);
Route::get('/brands/specificbrand/{brandname}', [BrandController::class, 'specificbrand']);

Route::get('/categories/categorycollection',[CategoryController::class, 'categoryproductcollection']);
// Route::get('/products/categoriedcollection/',[ProductController::class, 'productcategorycollection']);


Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('productvariant',ProductVariantController::class);


Route::apiResource('features', FeatureController::class);

Route::apiResource('specifications', SpecificationController::class);

Route::apiResource('categorytypes', CategoryTypeController::class);

