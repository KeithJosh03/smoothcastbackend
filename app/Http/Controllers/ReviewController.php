<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    /**
     * GET /api/reviews
     * Return all reviews, newest first.
     */
    public function index()
    {
        $reviews = Review::orderBy('review_date', 'desc')->get();

        return response()->json([
            'status' => true,
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * POST /api/reviews
     * Validate and create a new review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review_date' => ['required', 'date'],
            'comment' => ['required', 'string'],
        ]);

        $review = Review::create($validated);

        return response()->json(
            new ReviewResource($review),
            Response::HTTP_CREATED
        );
    }

    /**
     * DELETE /api/reviews/{review}
     * Delete a review by its ID.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
