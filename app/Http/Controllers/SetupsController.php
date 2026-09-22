<?php

namespace App\Http\Controllers;

use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SetupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setups = Setup::with(['items.product', 'items.sku', 'inclusions'])->paginate(10);
        return response()->json([
            'status' => true,
            'data' => $setups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bundle_title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:setups,slug',
            'description' => 'nullable|string',
            'hero_banner' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:setups,sku',
            'pricing_type' => 'required|in:fixed,calculated',
            'bundle_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'is_published' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            
            'bundle_items' => 'present|array',
            'bundle_items.*.product_id' => 'required|exists:products,product_id',
            'bundle_items.*.sku_id' => 'nullable|exists:product_skus,sku_id',
            'bundle_items.*.quantity' => 'required|integer|min:1',
            'bundle_items.*.is_required' => 'required|boolean',

            'custom_inclusions' => 'nullable|array',
            'custom_inclusions.*.title' => 'required|string|max:255',
            'custom_inclusions.*.price' => 'required|numeric|min:0',
            'custom_inclusions.*.quantity' => 'required|integer|min:1',
            'custom_inclusions.*.is_required' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['bundle_title']) . '-' . uniqid();
            }

            $setup = Setup::create([
                'bundle_title' => $validated['bundle_title'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'sku' => $validated['sku'],
                'pricing_type' => $validated['pricing_type'],
                'bundle_price' => $validated['bundle_price'],
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_published' => $validated['is_published'],
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);

            if (!empty($validated['hero_banner'])) {
                $setup->images()->create([
                    'image_url' => $validated['hero_banner'],
                    'isMain' => true,
                ]);
            }

            if (!empty($validated['bundle_items'])) {
                $setup->items()->createMany($validated['bundle_items']);
            }

            if (!empty($validated['custom_inclusions'])) {
                $setup->inclusions()->createMany($validated['custom_inclusions']);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Setup created successfully',
                'setup' => $setup->load(['items.product', 'items.sku', 'inclusions'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create setup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $setup = Setup::with(['items.product', 'items.sku', 'inclusions'])->findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $setup
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $setup = Setup::findOrFail($id);

        $validated = $request->validate([
            'bundle_title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:setups,slug,' . $setup->setup_id . ',setup_id',
            'description' => 'nullable|string',
            'hero_banner' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:setups,sku,' . $setup->setup_id . ',setup_id',
            'pricing_type' => 'required|in:fixed,calculated',
            'bundle_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'is_published' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            
            'bundle_items' => 'present|array',
            'bundle_items.*.product_id' => 'required|exists:products,product_id',
            'bundle_items.*.sku_id' => 'nullable|exists:product_skus,sku_id',
            'bundle_items.*.quantity' => 'required|integer|min:1',
            'bundle_items.*.is_required' => 'required|boolean',

            'custom_inclusions' => 'nullable|array',
            'custom_inclusions.*.title' => 'required|string|max:255',
            'custom_inclusions.*.price' => 'required|numeric|min:0',
            'custom_inclusions.*.quantity' => 'required|integer|min:1',
            'custom_inclusions.*.is_required' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $setup->update([
                'bundle_title' => $validated['bundle_title'],
                'slug' => $validated['slug'] ?? $setup->slug,
                'description' => $validated['description'] ?? null,
                'sku' => $validated['sku'],
                'pricing_type' => $validated['pricing_type'],
                'bundle_price' => $validated['bundle_price'],
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_published' => $validated['is_published'],
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);

            if (isset($validated['hero_banner'])) {
                $setup->images()->delete();
                if (!empty($validated['hero_banner'])) {
                    $setup->images()->create([
                        'image_url' => $validated['hero_banner'],
                        'isMain' => true,
                    ]);
                }
            }

            // Sync bundle items
            $setup->items()->delete();
            if (!empty($validated['bundle_items'])) {
                $setup->items()->createMany($validated['bundle_items']);
            }

            // Sync custom inclusions
            $setup->inclusions()->delete();
            if (!empty($validated['custom_inclusions'])) {
                $setup->inclusions()->createMany($validated['custom_inclusions']);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Setup updated successfully',
                'setup' => $setup->fresh(['items.product', 'items.sku', 'inclusions'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update setup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $setup = Setup::findOrFail($id);
        $setup->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Setup deleted successfully'
        ]);
    }
}
