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
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Middleware\EnsureUserIsAdmin;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// AUTHENTICATION
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/social-callback', [AuthController::class, 'register']);

// BRANDS (Public)
Route::get('/brands/specificbrand/{brandname}', [BrandController::class, 'specificbrand']);
Route::get('/brands/brandlogo', [BrandController::class, 'brandLogo']);
Route::get('/brands/brandNameListSearchHeader', [BrandController::class, 'BrandNameListSearchHeader']);

// CATEGORIES & SUBCATEGORIES (Public)
Route::get('/categories/header-list', [CategoryController::class, 'headerCategories']);
Route::get('/categories/specificCategory/{categoryname}', [CategoryController::class, 'specificCategoryProduct']);
Route::get('/categories/categorycollection', [CategoryController::class, 'categoryProductCollection']);
Route::get('/categories/categorysub/{categoryId}', [CategoryController::class, 'categorySub']);
Route::get('/categories/SubCatByCategoryId/{categoryId}', [CategoryController::class, 'subCatByCategoryId']);

// PROMOTIONS (Public Read)
Route::get('/promotions/active', [PromotionController::class, 'activePromotions']);

// PRODUCTS (Custom public endpoints must precede resource routes)
Route::get('/products/check-sku', [ProductController::class, 'checkSku']);
Route::get('/products/productsearch', [ProductController::class, 'productsearch']);
Route::get('/products/productlistdashboardsearch', [ProductController::class, 'productlistdashboardsearch']);
Route::get('/products/productdetailEditDashboard/{productId}', [ProductController::class, 'ProductDetailsEditDashboard']);
Route::get('/products/productviewdetails/{id}', [ProductController::class, 'productViewDetails']);
Route::post('/products/validate-sku', [ProductController::class, 'validateSku']);

// PUBLIC READ-ONLY RESOURCES
Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('subcategory', SubCategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Admins, Bloggers, Regular Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('reviews', ReviewController::class)->only(['store', 'destroy']);

    // Media Uploads (Accessible by any authenticated user)
    Route::post('/imageupload/uploads', [ImageUploadController::class, 'upload']);
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes (Write Access & Admin Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function () {
    // Admin Prefix Operations
    Route::prefix('admin')->group(function () {
        Route::patch('categories/{id}/status', [CategoryController::class, 'toggleStatus']);
        Route::post('categories/reorder', [CategoryController::class, 'reorder']);
        Route::patch('subcategories/{id}/status', [SubCategoryController::class, 'toggleStatus']);
        Route::post('subcategories/reorder', [SubCategoryController::class, 'reorder']);

        // Promotions Full Management
        Route::apiResource('promotions', PromotionController::class);
    });

    // Resource Management (Create, Update, Delete)
    Route::apiResource('brands', BrandController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('subcategory', SubCategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);

    // Utility Resources
    Route::apiResource('productimage', ProductImageController::class);
    Route::apiResource('productvariant', ProductVariantController::class);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('specifications', SpecificationController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('productDiscounted', ProductDiscountController::class);
    Route::apiResource('setups', SetupController::class);
    Route::apiResource('inclusions', InclusionController::class);
    Route::apiResource('setupimages', SetupImageController::class);
});