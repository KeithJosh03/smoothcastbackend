<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Blog;
use App\Models\BlogLike;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('sanctum')->id();

        $blogs = Blog::with(['user', 'images', 'productTags.product.images', 'likes' => function($query) use ($userId) {
            if ($userId) {
                $query->where('user_id', $userId);
            }
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        $formattedBlogs = $blogs->map(function ($blog) use ($userId) {
            return [
                'id' => $blog->id,
                'user' => [
                    'id' => $blog->user->id ?? 0,
                    'name' => $blog->user->name ?? 'Unknown Angler',
                ],
                'title' => $blog->title,
                'caption_html' => $blog->caption_html,
                'likes_count' => $blog->likes_count,
                'is_liked' => $userId ? $blog->likes->isNotEmpty() : false,
                'created_at' => $blog->created_at->diffForHumans(),
                'images' => $blog->images->map(function($img) {
                    return [
                        'url' => $img->image_url,
                        'is_main' => $img->isMain
                    ];
                }),
                'tagged_products' => $blog->productTags->map(function($tag) {
                    if (!$tag->product) return null;
                    return [
                        'id' => $tag->product->product_id,
                        'title' => $tag->product->product_title,
                        'price' => $tag->product->base_price,
                        'image' => $tag->product->images->where('isMain', 1)->first()->image_url ?? ($tag->product->images->first()->image_url ?? null)
                    ];
                })->filter()
            ];
        });

        return response()->json([
            'blogs' => $formattedBlogs,
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
        ]);
    }

    public function toggleLike($id)
    {
        $userId = Auth::guard('sanctum')->id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
        $existingLike = BlogLike::where('blog_id', $blog->id)->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $blog->decrement('likes_count');
            return response()->json(['message' => 'Unliked', 'is_liked' => false, 'likes_count' => $blog->likes_count]);
        } else {
            BlogLike::create(['blog_id' => $blog->id, 'user_id' => $userId]);
            $blog->increment('likes_count');
            return response()->json(['message' => 'Liked', 'is_liked' => true, 'likes_count' => $blog->likes_count]);
        }
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        
        $products = \App\Models\Product::where('product_title', 'LIKE', "%{$query}%")
            ->where('is_active', true)
            ->limit(10)
            ->get(['product_id as id', 'product_title as display']);
            
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $userId = Auth::guard('sanctum')->id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'caption_html' => 'required|string',
            'images' => 'array',
            'tagged_products' => 'array',
        ]);

        $blog = Blog::create([
            'user_id' => $userId,
            'title' => $validated['title'],
            'caption_html' => $validated['caption_html'],
        ]);

        if (!empty($validated['images'])) {
            foreach ($validated['images'] as $index => $imageUrl) {
                \App\Models\Image::create([
                    'imageable_id' => $blog->id,
                    'imageable_type' => Blog::class,
                    'image_url' => $imageUrl,
                    'isMain' => $index === 0,
                ]);
            }
        }

        if (!empty($validated['tagged_products'])) {
            // Remove duplicates
            $taggedProducts = array_unique($validated['tagged_products']);
            foreach ($taggedProducts as $productId) {
                \App\Models\BlogProductTag::create([
                    'blog_id' => $blog->id,
                    'product_id' => $productId,
                ]);
            }
        }

        return response()->json(['message' => 'Blog created successfully', 'blog' => $blog]);
    }
}
