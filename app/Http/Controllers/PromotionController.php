<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{

    public function index()
    {
        $promotions = Promotion::with(['categories', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($promotions);
    }

    /**
     * Store a new promotion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:PERCENTAGE,FIXED_AMOUNT',
            'discount_value' => 'required|numeric|min:0.01',
            'apply_to'        => 'required|in:ALL,CATEGORY,PRODUCT',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'is_active'      => 'boolean',
            'target_ids'     => [
                'nullable',
                'array',
                Rule::requiredIf(fn () => in_array($request->apply_to, ['CATEGORY', 'PRODUCT'])),
            ],
            'target_ids.*'   => 'integer',
        ]);

        $promotion = Promotion::create([
            'name'           => $validated['name'],
            'discount_type'  => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'apply_to'        => $validated['apply_to'],
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date'],
            'is_active'      => $validated['is_active'] ?? true,
        ]);

        // Sync pivot target relationships
        if ($validated['apply_to'] === 'CATEGORY' && !empty($validated['target_ids'])) {
            $promotion->categories()->sync($validated['target_ids']);
        } elseif ($validated['apply_to'] === 'PRODUCT' && !empty($validated['target_ids'])) {
            $promotion->products()->sync($validated['target_ids']);
        }

        return response()->json([
            'message'   => 'Promotion created successfully!',
            'promotion' => $promotion->load(['categories', 'products']),
        ], 201);
    }

    public function show($id)
    {
        $promotion = Promotion::with(['categories', 'products'])->findOrFail($id);
        return response()->json($promotion);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'discount_type'  => 'sometimes|required|in:PERCENTAGE,FIXED_AMOUNT',
            'discount_value' => 'sometimes|required|numeric|min:0.01',
            'apply_to'        => 'sometimes|required|in:ALL,CATEGORY,PRODUCT',
            'start_date'     => 'sometimes|required|date',
            'end_date'       => 'sometimes|required|date|after:start_date',
            'is_active'      => 'nullable|boolean',
            'target_ids'     => 'nullable|array',
            'target_ids.*'   => 'integer',
        ]);

        $promotion->update($request->only([
            'name', 'discount_type', 'discount_value', 'apply_to', 'start_date', 'end_date', 'is_active'
        ]));

        // Re-sync pivots if scope or target_ids were supplied
        $applyTo = $request->get('apply_to', $promotion->apply_to);
        
        if ($applyTo === 'CATEGORY') {
            $promotion->products()->detach();
            if ($request->has('target_ids')) {
                $promotion->categories()->sync($request->target_ids);
            }
        } elseif ($applyTo === 'PRODUCT') {
            $promotion->categories()->detach();
            if ($request->has('target_ids')) {
                $promotion->products()->sync($request->target_ids);
            }
        } else { // ALL
            $promotion->categories()->detach();
            $promotion->products()->detach();
        }

        return response()->json([
            'message'   => 'Promotion updated successfully!',
            'promotion' => $promotion->load(['categories', 'products']),
        ]);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        
        $promotion->categories()->detach();
        $promotion->products()->detach();
        $promotion->delete();

        return response()->json(['message' => 'Promotion deleted successfully.']);
    }


    public function activePromotions()
    {
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->select('promotion_id', 'name', 'discount_type', 'discount_value', 'apply_to', 'start_date', 'end_date')
            ->with([
                'categories:category_id,category_name',
                'products:product_id,product_title',
            ])
            ->get();

        // Transform to frontend-friendly format
        $formatted = $promotions->map(function($p) {
            return [
                'id' => $p->promotion_id,
                'name' => $p->name,
                'type' => $p->discount_type,
                'value' => (float) $p->discount_value,
                'scope' => $p->apply_to,
                'startDate' => $p->start_date->format('Y-m-d'),
                'endDate' => $p->end_date->format('Y-m-d'),
                'targetIds' => $p->apply_to === 'ALL' 
                    ? [] 
                    : ($p->apply_to === 'CATEGORY' 
                        ? $p->categories->pluck('category_id')->toArray() 
                        : $p->products->pluck('product_id')->toArray()),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'promotions' => $formatted,
            ],
        ]);
    }
}