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
use App\Http\Controllers\ProductImageController;



Route::get('/categories/specificCategory/{categoryname}', [CategoryController::class, 'specificCategory']);
Route::get('/brands/specificbrand/{brandname}', [BrandController::class, 'specificbrand']);
Route::get('/brands/brandlogo/', [BrandController::class, 'brandLogo']);


Route::get('/categories/categorycollection',[CategoryController::class, 'categoryproductcollection']);
Route::get('/products/productdetail/{productId}', [ProductController::class, 'productSpecificDetail']);
Route::get('/products/productsearch/{productname}', [ProductController::class, 'productSearch']);


Route::apiResource('brands', BrandController::class);

Route::apiResource('productimage', ProductImageController::class);


Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('productvariant',ProductVariantController::class);


Route::apiResource('features', FeatureController::class);

Route::apiResource('specifications', SpecificationController::class);

Route::apiResource('categorytypes', CategoryTypeController::class);

