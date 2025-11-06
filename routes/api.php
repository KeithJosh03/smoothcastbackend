<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\InclusionController;
use App\Http\Controllers\SetupImageController;
use App\Http\Controllers\ProductDiscountController;
use App\Http\Controllers\AuthController;

Route::get('/brands/specificbrand/{brandname}', [BrandController::class, 'specificbrand']);
Route::get('/brands/brandlogo/', [BrandController::class, 'brandLogo']);
Route::get('/brands/headerbrand/', [BrandController::class, 'headerBrand']);
Route::put('/brands/{brand}', [BrandController::class, 'update']);

Route::get('/categories/specificCategory/{categoryname}', [CategoryController::class, 'specificCategory']);
Route::get('/categories/categorycollection',[CategoryController::class, 'categoryproductcollection']);
Route::get('/categories/categorysub/{categoryId}', [CategoryController::class, 'categorySub']);


Route::get('/products/productsearchinitial/{productname}', [ProductController::class, 'productDetailInitial']);
Route::get('/products/newarrival/', [ProductController::class, 'newArrivals']);

Route::get('/products/productdetail/{productId}', [ProductController::class, 'productSpecificDetail']);
Route::get('/products/productsearch/{productname}', [ProductController::class, 'productSearch']);

Route::get('/setups/setupcollection/', [SetupController::class, 'setupcollection']);
Route::get('/setups/setupShowcase/', [SetupController::class, 'setups']);
Route::get('/setups/specificSetup/{setupId}', [SetupController::class, 'specificSetup']);

Route::get('/productDiscounted/collection', [ProductDiscountController::class,'discountedProductCollection']);
Route::get('/productDiscounted/discountedProducts', [ProductDiscountController::class,'discountedProducts']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);


Route::post('products',[ProductController::class,'store']);

Route::apiResource('brands', BrandController::class);
Route::apiResource('productimage', ProductImageController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('productvariant', ProductVariantController::class);
Route::apiResource('features', FeatureController::class);
Route::apiResource('specifications', SpecificationController::class);
Route::apiResource('packages', PackageController::class);
Route::apiResource('productDiscounted', ProductDiscountController::class);
Route::apiResource('subcategory', SubCategoryController::class);
Route::apiResource('setups', SetupController::class);
Route::apiResource('inclusions', InclusionController::class);
Route::apiResource('setupimages', SetupImageController::class);
