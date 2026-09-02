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
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureUserIsAdmin;

Route::post('/imageupload/uploads', [ImageUploadController::class, 'upload']);

// BRANDS API
Route::get('/brands/specificbrand/{brandname}', [BrandController::class, 'specificbrand']);
Route::get('/brands/brandlogo/', [BrandController::class, 'brandLogo']);
Route::get('/brands/brandNameList/', [BrandController::class, 'BrandNameList']);
Route::put('/brands/{brand}', [BrandController::class, 'update']);

// CATEGORIES API
Route::get('/categories/specificCategory/{categoryname}', [CategoryController::class, 'specificCategory']);
Route::get('/categories/categorycollection', [CategoryController::class, 'categoryproductcollection']);
Route::get('/categories/categorysub/{categoryId}', [CategoryController::class, 'categorySub']);
Route::get('/categories/SubCatByCategoryId/{categoryId}', [CategoryController::class, 'subCatByCategoryId']);

// PRODUCTS API
Route::get('/products/check-sku', [ProductController::class, 'checkSku']);
Route::get('/products/productsearchinitial/{productname}', [ProductController::class, 'productDetailInitial']);
Route::get('/products/newarrival/', [ProductController::class, 'newArrivals']);
Route::get('/products/productdetail/{productId}', [ProductController::class, 'productSpecificDetail']);
Route::get('/products/productdetailEditDashboard/{productId}', [ProductController::class, 'ProductDetailsEditDashboard']);
Route::get('/products/productsearch', [ProductController::class, 'productSearch']);

// Dashboard Product API
Route::get('/products/productlistdashboardsearch/', [ProductController::class, 'productListDashBoardSearch']);
Route::delete('/products/delete/{productId}', [ProductController::class, 'destroy']);

// AUTHENTICATION API
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/social-callback', [AuthController::class, 'register']); // <-- Added for Facebook/Google OAuth sync
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::post('products/store', [ProductController::class, 'store']);

Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
Route::apiResource('productimage', ProductImageController::class);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class);
Route::apiResource('productvariant', ProductVariantController::class);
Route::apiResource('features', FeatureController::class);
Route::apiResource('specifications', SpecificationController::class);
Route::apiResource('packages', PackageController::class);
Route::apiResource('productDiscounted', ProductDiscountController::class);
Route::apiResource('subcategory', SubCategoryController::class)->only(['index', 'show']);
Route::apiResource('setups', SetupController::class);
Route::apiResource('inclusions', InclusionController::class);
Route::apiResource('setupimages', SetupImageController::class);

// REVIEWS API
Route::apiResource('reviews', ReviewController::class)->only(['index', 'store', 'destroy']);

// ADMIN PROTECTED ROUTES
Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function () {
    Route::prefix('admin')->group(function () {

    });

    Route::apiResource('brands', BrandController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('subcategory', SubCategoryController::class)->only(['store', 'update', 'destroy']);
});