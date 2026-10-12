<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function resolveCart(Request $request)
    {
        $userId = Auth::guard('sanctum')->id();
        $sessionId = $request->header('X-Session-ID') ?? $request->input('session_id');

        if ($userId) {
            return Cart::where('user_id', $userId)->first();
        }

        if ($sessionId) {
            return Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
        }

        return null;
    }

    private function getShippingFeeBreakdown($region, $totalWeightGrams, $hasBulky = false)
    {
        $weightKg = $totalWeightGrams / 1000;
        
        $region = strtoupper($region ?: 'LUZON');
        if (!in_array($region, ['MINDANAO', 'VISAYAS', 'LUZON'])) {
            $region = 'LUZON';
        }

        $baseRate = ['MINDANAO' => 85.00, 'VISAYAS' => 105.00, 'LUZON' => 105.00][$region];
        $totalFee = 0;

        if ($weightKg <= 0.5) {
            $rates = ['MINDANAO' => 85, 'VISAYAS' => 105, 'LUZON' => 105];
            $totalFee = $rates[$region];
        } elseif ($weightKg <= 1.0) {
            $rates = ['MINDANAO' => 155, 'VISAYAS' => 175, 'LUZON' => 195];
            $totalFee = $rates[$region];
        } elseif ($weightKg <= 3.0) {
            $rates = ['MINDANAO' => 180, 'VISAYAS' => 195, 'LUZON' => 215];
            $totalFee = $rates[$region];
        } elseif ($weightKg <= 4.0) {
            $rates = ['MINDANAO' => 270, 'VISAYAS' => 285, 'LUZON' => 325];
            $totalFee = $rates[$region];
        } else {
            $baseRates = ['MINDANAO' => 270, 'VISAYAS' => 285, 'LUZON' => 325];
            $extraKg = ceil($weightKg - 4.0);
            $totalFee = $baseRates[$region] + ($extraKg * 80);
        }

        $excessWeightFee = max(0, $totalFee - $baseRate);
        $bulkyFee = $hasBulky ? 75.00 : 0.00;

        return [
            'total_shipping_fee' => (float) $totalFee,
            'base_rate' => (float) $baseRate,
            'excess_weight_fee' => (float) $excessWeightFee,
            'bulky_handling_fee' => (float) $bulkyFee,
            'weight_kg' => round($weightKg, 2),
            'weight_grams' => $totalWeightGrams,
            'package_type' => $hasBulky ? 'Special Courier / Rod Tube Box' : 'Standard J&T Parcel Box',
        ];
    }

    private function getShippingFee($region, $totalWeightGrams)
    {
        $breakdown = $this->getShippingFeeBreakdown($region, $totalWeightGrams);
        return $breakdown['total_shipping_fee'];
    }

    private function evaluateCart(Cart $cart, $region = null, $voucherCode = null, $province = null)
    {
        $cart->load(['items.product.promotions', 'items.sku', 'items.setup.items.product', 'items.setup.items.sku']);

        $subtotal = 0;
        $totalDiscount = 0;
        $totalWeight = 0;
        $hasBulky = false;
        
        $evaluatedItems = [];

        foreach ($cart->items as $item) {
            if ($item->setup_id) {
                // Handle Setup
                $setup = $item->setup;
                if (!$setup || !$setup->is_published) {
                    throw new \Exception("Setup '{$setup->bundle_title}' is unavailable.");
                }
                
                $price = $setup->bundle_price;
                
                // Calculate weight from setup items based on selections
                $setupWeight = 0;
                $setupBulky = false;
                
                if (is_array($item->selections)) {
                    foreach ($item->selections as $sel) {
                        if (isset($sel['product_id'])) {
                            $p = \App\Models\Product::find($sel['product_id']);
                            if ($p) {
                                $w = $p->weight_g ?? $p->weight ?? 0;
                                $b = in_array($p->packaging_type ?? '', ['BOX_L', 'LONG_PARCEL']) || ($p->is_bulky ?? false);
                                if (isset($sel['sku_id']) && $sel['sku_id']) {
                                    $s = \App\Models\ProductSku::find($sel['sku_id']);
                                    if ($s) {
                                        $w = $s->weight_g !== null ? $s->weight_g : ($s->weight !== null ? $s->weight : $w);
                                        $b = $s->effective_is_bulky;
                                    }
                                }
                                $setupWeight += $w;
                                if ($b) $setupBulky = true;
                            }
                        }
                    }
                }

                $itemSubtotal = $price * $item->quantity;
                $subtotal += $itemSubtotal;
                $totalWeight += ($setupWeight * $item->quantity);
                if ($setupBulky) $hasBulky = true;

                $evaluatedItems[] = [
                    'cart_item' => $item,
                    'cart_item_id' => $item->cart_item_id,
                    'title_snapshot' => $setup->bundle_title,
                    'price_snapshot' => (float) $price,
                    'weight_snapshot' => (int) $setupWeight,
                    'quantity' => (int) $item->quantity,
                    'discount_applied_snapshot' => 0.0,
                    'final_price' => (float) $price,
                    'promotion_name' => null,
                    'discount_type' => null,
                    'discount_value' => null,
                    'is_bulky' => (bool) $setupBulky,
                ];

            } else {
                // Handle Product / SKU
                $product = $item->product;
                if (!$product || !$product->is_active) {
                    throw new \Exception("Product '{$product->product_title}' is unavailable.");
                }

                $sku = $item->sku;
                if ($sku && !$sku->is_active) {
                    throw new \Exception("Variant for '{$product->product_title}' is unavailable.");
                }

                $originalPrice = (float) ($sku ? $sku->price : $product->base_price);
                
                // Weight and Bulky logic
                $weight = $sku && $sku->weight_g !== null ? $sku->weight_g : ($sku && $sku->weight !== null ? $sku->weight : ($product->weight_g ?? $product->weight ?? 0));
                $isBulky = $sku ? $sku->effective_is_bulky : ($product->is_bulky ?? in_array($product->packaging_type ?? '', ['BOX_L', 'LONG_PARCEL']));

                // Check Best Promotion
                $activePromo = $sku ? $sku->active_promotion : $product->active_promotion;
                $discountAmount = 0.0;
                
                if ($activePromo) {
                    if ($activePromo->discount_type === 'PERCENTAGE') {
                        $discountAmount = round($originalPrice * ((float) $activePromo->discount_value / 100), 2);
                    } else {
                        $discountAmount = round((float) $activePromo->discount_value, 2);
                    }
                }
                
                $subtotal += ($originalPrice * $item->quantity);
                $totalDiscount += ($discountAmount * $item->quantity);
                
                $totalWeight += ($weight * $item->quantity);
                if ($isBulky) $hasBulky = true;

                $evaluatedItems[] = [
                    'cart_item' => $item,
                    'cart_item_id' => $item->cart_item_id,
                    'title_snapshot' => $product->product_title . ($sku ? " ({$sku->sku_code})" : ""),
                    'price_snapshot' => (float) $originalPrice,
                    'weight_snapshot' => (int) $weight,
                    'quantity' => (int) $item->quantity,
                    'discount_applied_snapshot' => (float) $discountAmount,
                    'final_price' => max(0, (float) ($originalPrice - $discountAmount)),
                    'promotion_name' => $activePromo ? $activePromo->name : null,
                    'discount_type' => $activePromo ? $activePromo->discount_type : null,
                    'discount_value' => $activePromo ? (float) $activePromo->discount_value : null,
                    'is_bulky' => (bool) $isBulky,
                ];
            }
        }

        $jntService = app(\App\Services\JnTFreightCalculatorService::class);
        $shippingBreakdown = null;
        $shippingFee = 0.0;
        $bulkyHandlingFee = 0.0;

        if ($region) {
            $shippingBreakdown = $jntService->calculateShipping($cart->items, $region, $province);
            $shippingFee = (float) $shippingBreakdown['total_shipping_fee'];
            $bulkyHandlingFee = (float) $shippingBreakdown['bulky_surcharge'];
            if ($shippingBreakdown['has_bulky_item']) {
                $hasBulky = true;
            }
        } else {
            $bulkyHandlingFee = $hasBulky ? (float) config('jnt.bulky_surcharge', 150.00) : 0.00;
        }

        // Voucher discount calculation
        $voucherDiscount = 0.0;
        $voucherApplied = null;
        if ($voucherCode) {
            $cleanCode = strtoupper(trim($voucherCode));
            $netBeforeVoucher = max(0, $subtotal - $totalDiscount);

            if ($cleanCode === 'FIRSTCAST200') {
                $voucherDiscount = min(200.00, $netBeforeVoucher);
                $voucherApplied = [
                    'code' => 'FIRSTCAST200',
                    'name' => 'First Cast Welcome Voucher',
                    'discount_amount' => $voucherDiscount,
                    'tag' => '-₱200.00 OFF'
                ];
            } elseif ($cleanCode === 'SMOOTHCAST10') {
                $voucherDiscount = round($netBeforeVoucher * 0.10, 2);
                $voucherApplied = [
                    'code' => 'SMOOTHCAST10',
                    'name' => 'Smooth Cast 10% Extra OFF',
                    'discount_amount' => $voucherDiscount,
                    'tag' => '10% OFF'
                ];
            } elseif ($cleanCode === 'ANGLER500') {
                $voucherDiscount = min(500.00, $netBeforeVoucher);
                $voucherApplied = [
                    'code' => 'ANGLER500',
                    'name' => 'Pro Angler Tournament Voucher',
                    'discount_amount' => $voucherDiscount,
                    'tag' => '-₱500.00 OFF'
                ];
            } else {
                $dbPromo = \App\Models\Promotion::currentlyActive()
                    ->where(function ($q) use ($cleanCode) {
                        $q->whereRaw('UPPER(name) = ?', [$cleanCode])
                          ->orWhereRaw("UPPER(REPLACE(name, ' ', '')) = ?", [$cleanCode]);
                    })
                    ->first();
                if ($dbPromo) {
                    if ($dbPromo->discount_type === 'PERCENTAGE') {
                        $voucherDiscount = round($netBeforeVoucher * ((float) $dbPromo->discount_value / 100), 2);
                    } else {
                        $voucherDiscount = min((float) $dbPromo->discount_value, $netBeforeVoucher);
                    }
                    $voucherApplied = [
                        'code' => $cleanCode,
                        'name' => $dbPromo->name,
                        'discount_amount' => $voucherDiscount,
                        'tag' => $dbPromo->discount_type === 'PERCENTAGE' ? "{$dbPromo->discount_value}% OFF" : "-₱{$voucherDiscount} OFF"
                    ];
                }
            }
        }

        $grandTotal = max(0, $subtotal - $totalDiscount - $voucherDiscount + $shippingFee);

        return [
            'subtotal' => (float) $subtotal,
            'total_discount' => (float) $totalDiscount,
            'sales_discount' => (float) $totalDiscount,
            'voucher_discount' => (float) $voucherDiscount,
            'voucher_applied' => $voucherApplied,
            'total_weight' => (int) $totalWeight,
            'has_bulky' => (bool) $hasBulky,
            'bulky_handling_fee' => (float) $bulkyHandlingFee,
            'shipping_fee' => (float) $shippingFee,
            'shipping_breakdown' => $shippingBreakdown,
            'grand_total' => (float) $grandTotal,
            'evaluated_items' => $evaluatedItems
        ];
    }

    public function calculate(Request $request)
    {
        $cart = $this->resolveCart($request);
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty.'], 422);
        }

        $region = $request->input('region');
        $province = $request->input('province') ?? $request->input('province_code');
        $voucherCode = $request->input('voucher_code');

        try {
            $evaluation = $this->evaluateCart($cart, $region, $voucherCode, $province);
            
            // Format evaluated_items for clean JSON response
            $evaluatedItemsForResponse = array_map(function ($ei) {
                return [
                    'cart_item_id' => $ei['cart_item_id'],
                    'title_snapshot' => $ei['title_snapshot'],
                    'price_snapshot' => (float) $ei['price_snapshot'],
                    'weight_snapshot' => (int) $ei['weight_snapshot'],
                    'quantity' => (int) $ei['quantity'],
                    'discount_applied_snapshot' => (float) $ei['discount_applied_snapshot'],
                    'final_price' => (float) $ei['final_price'],
                    'promotion_name' => $ei['promotion_name'] ?? null,
                    'discount_type' => $ei['discount_type'] ?? null,
                    'discount_value' => $ei['discount_value'] ?? null,
                    'is_bulky' => (bool) ($ei['is_bulky'] ?? false),
                ];
            }, $evaluation['evaluated_items']);

            $evaluation['evaluated_items'] = $evaluatedItemsForResponse;

            return response()->json([
                'status' => true,
                'data' => $evaluation
            ]);
        } catch (\Throwable $e) {
            \Log::error('Calculate Cart Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone_number' => 'required|string',
            'street_address' => 'required|string',
            'barangay' => 'required|string',
            'city_municipality' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'region' => 'required|string',
            'payment_method' => 'nullable|string|in:gcash,card,cod',
            'gcash_sender_number' => 'nullable|string',
            'gcash_reference_number' => 'nullable|string',
            'voucher_code' => 'nullable|string',
        ]);

        $paymentMethod = $validated['payment_method'] ?? 'gcash';

        if ($paymentMethod === 'gcash') {
            if (empty($validated['gcash_sender_number']) || empty($validated['gcash_reference_number'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'GCash Sender Mobile Number and 13-digit Reference Number are required for GCash payment.'
                ], 422);
            }
        } else {
            // For card and COD, assign default reference markers
            if (empty($validated['gcash_sender_number'])) {
                $validated['gcash_sender_number'] = $validated['phone_number'];
            }
            if (empty($validated['gcash_reference_number'])) {
                $validated['gcash_reference_number'] = strtoupper($paymentMethod) . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            }
        }

        $cart = $this->resolveCart($request);
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty.'], 422);
        }

        try {
            DB::beginTransaction();

            $evaluation = $this->evaluateCart($cart, $validated['region'], $validated['voucher_code'] ?? null, $validated['province'] ?? null);

            $order = Order::create([
                'user_id' => Auth::guard('sanctum')->id(),
                'subtotal' => $evaluation['subtotal'],
                'shipping_fee' => $evaluation['shipping_fee'],
                'bulky_handling_fee' => $evaluation['bulky_handling_fee'],
                'discount_amount' => $evaluation['total_discount'] + ($evaluation['voucher_discount'] ?? 0),
                'grand_total' => $evaluation['grand_total'],
                
                'full_name' => $validated['full_name'],
                'phone_number' => $validated['phone_number'],
                'street_address' => $validated['street_address'],
                'barangay' => $validated['barangay'],
                'city_municipality' => $validated['city_municipality'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
                'region' => $validated['region'],
                
                'gcash_sender_number' => $validated['gcash_sender_number'],
                'gcash_reference_number' => $validated['gcash_reference_number'],
                
                'payment_status' => 'pending_verification',
                'fulfillment_status' => 'unfulfilled',
            ]);

            foreach ($evaluation['evaluated_items'] as $evalItem) {
                $cartItem = $evalItem['cart_item'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'sku_id' => $cartItem->sku_id,
                    'setup_id' => $cartItem->setup_id,
                    'title_snapshot' => $evalItem['title_snapshot'],
                    'price_snapshot' => $evalItem['price_snapshot'],
                    'weight_snapshot' => $evalItem['weight_snapshot'],
                    'quantity' => $evalItem['quantity'],
                    'discount_applied_snapshot' => $evalItem['discount_applied_snapshot'],
                ]);
            }

            // Clear the cart
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully.',
                'order_id' => $order->id
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
