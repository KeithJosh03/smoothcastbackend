<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\CategoryCollectionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\CategorySubResource;
use App\Http\Resources\SpecificCategoryProductResource;


class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::withCount('subCategories')
            ->orderBy('sort_order', 'asc')
            ->orderBy('category_id', 'asc')
            ->get();
        return response()->json([
            'status' => true,
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer']
        ]);

        $category = Category::create($validated);
        return response()->json(new CategoryResource($category), Response::HTTP_CREATED);
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function edit(Category $category)
    {
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => ['sometimes', 'required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        $category->update($validated);

        return response()->json(new CategoryResource($category->loadCount('subCategories')));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function toggleStatus(Request $request, $id)
    {
        $category = Category::withCount('subCategories')->findOrFail($id);
        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean']
        ]);

        $newStatus = array_key_exists('is_active', $validated) ? $validated['is_active'] : !$category->is_active;
        $category->update(['is_active' => $newStatus]);

        return response()->json(new CategoryResource($category));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required'],
            'orders.*.sort_order' => ['required', 'integer']
        ]);

        foreach ($validated['orders'] as $item) {
            Category::where('category_id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['status' => true, 'message' => 'Categories reordered successfully']);
    }

    public function categoryProductCollection()
    {
        $categories = Category::select('category_id', 'category_name')
            ->with([
                'products' => function ($query) {
                    $query->select(
                        'product_id',
                        'product_title',
                        'category_id',
                        'brand_id',
                        'base_price',
                        'sub_category_id'
                    )
                        ->with([
                            'brand:brand_id,brand_name',
                            'subCategory:sub_category_id,sub_category_name',
                            'mainImage:image_id,imageable_id,imageable_type,image_url,isMain',
                            'productTypeVariant.firstVariantOption',
                            'firstProductSku.mainImage:image_id,imageable_id,imageable_type,image_url,isMain'
                        ])
                        ->take(4);
                }
            ])
            ->get();
        return response()->json([
            'categories' => new CategoryCollectionResource($categories)
            // 'categories' => $categories
        ]);
    }




    public function specificCategoryProduct($categoryname, Request $request)
    {
        $perPage = 15;
        $page = $request->get('page', 1);

        $category = Category::where('category_name', $categoryname)->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $products = $category
            ->products()
            ->select('base_price', 'product_id', 'brand_id', 'sub_category_id', 'product_title')
            ->with([
                'brand:brand_id,brand_name',
                'subCategory:sub_category_id,sub_category_name',
                'mainImage:image_id,imageable_id,imageable_type,image_url,isMain',
                'productTypeVariantFirst.firstVariantOption.image'
            ])
            ->paginate($perPage, ['*'], 'page', $page);

        if ($products->isEmpty()) {
            return response()->json([
                'status' => true,
                'categoryproducts' => [
                    'categoryId' => $category->category_id,
                    'categoryName' => $category->category_name,
                    'products' => []
                ],
                'currentPage' => 1,
                'lastPage' => 1,
                'hasMore' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'categoryproducts' => [
                'categoryId' => $category->category_id,
                'categoryName' => $category->category_name,
                'products' => SpecificCategoryProductResource::collection($products->items())
            ],
            'currentPage' => $products->currentPage(),
            'lastPage' => $products->lastPage(),
            'hasMore' => $products->hasMorePages()
        ]);
    }

    public function categorySub($categoryId)
    {
        $category = Category::where('category_id', $categoryId)
            ->with(['subCategories' => function($q) {
                $q->orderBy('sort_order', 'asc')->orderBy('sub_category_id', 'asc');
            }])
            ->first();

        if (!$category) {
            return response()->json([
                'status' => true,
                'category' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'categorySubs' => CategorySubResource::collection($category->subCategories)
        ]);
    }


    public function subCatByCategoryId($categoryId)
    {
        $category = Category::where('category_id', $categoryId)
            ->with(['subCategories' => function($q) {
                $q->orderBy('sort_order', 'asc')->orderBy('sub_category_id', 'asc');
            }])
            ->first();

        if (!$category) {
            return response()->json([
                'status' => true,
                'category' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'categorySub' => SubCategoryResource::collection($category->subCategories)
        ]);
    }
}