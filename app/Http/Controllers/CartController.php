<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    private function resolveCart(Request $request)
    {
        $userId = Auth::guard('sanctum')->id();
        $sessionId = $request->header('X-Session-ID') ?? $request->input('session_id');

        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            if ($sessionId) {
                $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
                if ($guestCart && $guestCart->cart_id !== $cart->cart_id) {
                    foreach ($guestCart->items as $item) {
                        if ($item->setup_id) {
                            $existing = $this->findMatchingSetupCartItem(
                                $cart,
                                (int) $item->setup_id,
                                $item->selections
                            );

                            if ($existing) {
                                $existing->quantity += $item->quantity;
                                $existing->save();
                                $item->delete();
                            } else {
                                $item->cart_id = $cart->cart_id;
                                $item->save();
                            }
                        } else {
                            $existing = $cart->items()
                                ->where('product_id', $item->product_id)
                                ->where('sku_id', $item->sku_id)
                                ->first();

                            if ($existing) {
                                $existing->quantity += $item->quantity;
                                $existing->save();
                                $item->delete();
                            } else {
                                $item->cart_id = $cart->cart_id;
                                $item->save();
                            }
                        }
                    }
                    $guestCart->delete();
                }
            }
            return $cart;
        }

        if (!$sessionId) {
            return null;
        }

        return Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
    }

    private function cartItemRelations(): array
    {
        return [
            'items.product.images',
            'items.sku',
            'items.setup.images',
        ];
    }

    private function setupIsPurchasable(Setup $setup): ?string
    {
        if (!$setup->is_published) {
            return 'This setup is not available.';
        }

        $now = Carbon::now();
        if ($setup->start_date && $now->lt($setup->start_date)) {
            return 'This setup is not available yet.';
        }
        if ($setup->end_date && $now->gt($setup->end_date)) {
            return 'This setup is no longer available.';
        }

        return null;
    }

    private function selectionsFingerprint(?array $selections): string
    {
        if (empty($selections)) {
            return '';
        }

        $normalized = collect($selections)
            ->map(function ($row) {
                return [
                    'group_name' => $row['group_name'] ?? '',
                    'setup_item_id' => (int) ($row['setup_item_id'] ?? 0),
                ];
            })
            ->sortBy('group_name')
            ->values()
            ->all();

        return json_encode($normalized);
    }

    private function findMatchingSetupCartItem(Cart $cart, int $setupId, ?array $selections): ?CartItem
    {
        $fingerprint = $this->selectionsFingerprint($selections);

        return $cart->items()
            ->where('setup_id', $setupId)
            ->get()
            ->first(function (CartItem $item) use ($fingerprint) {
                return $this->selectionsFingerprint($item->selections) === $fingerprint;
            });
    }

    /**
     * @return array{0: array|null, 1: string|null} normalized selections or error message
     */
    private function validateAndNormalizeSetupChoices(Setup $setup, ?array $choices): array
    {
        $setup->loadMissing('items');

        $choiceGroups = $setup->items
            ->filter(fn ($item) => filled($item->group_name))
            ->groupBy(fn ($item) => trim($item->group_name));

        if ($choiceGroups->isEmpty()) {
            return [null, null];
        }

        if (empty($choices) || ! is_array($choices)) {
            return [null, 'Please select all bundle options before adding to cart.'];
        }

        $normalized = [];

        foreach ($choiceGroups as $groupName => $itemsInGroup) {
            $matches = collect($choices)->filter(
                fn ($c) => isset($c['group_name']) && trim((string) $c['group_name']) === $groupName
            );

            if ($matches->count() !== 1) {
                return [null, "Please select exactly one option for \"{$groupName}\"."];
            }

            $choice = $matches->first();
            $setupItemId = (int) ($choice['setup_item_id'] ?? 0);
            $matchedItem = $itemsInGroup->firstWhere('id', $setupItemId);

            if (! $matchedItem) {
                return [null, "Invalid selection for \"{$groupName}\"."];
            }

            $normalized[] = [
                'group_name' => $groupName,
                'setup_item_id' => $matchedItem->id,
                'product_id' => $matchedItem->product_id,
                'sku_id' => $matchedItem->sku_id,
                'label' => $choice['label'] ?? null,
            ];
        }

        usort($normalized, fn ($a, $b) => strcmp($a['group_name'], $b['group_name']));

        return [$normalized, null];
    }

    public function getCart(Request $request)
    {
        $cart = $this->resolveCart($request);

        if (!$cart) {
            return response()->json([
                'status' => true,
                'cart' => null,
            ]);
        }

        $cart->load($this->cartItemRelations());

        return response()->json([
            'status' => true,
            'cart' => $cart,
        ]);
    }

    public function addToCart(Request $request)
    {
        $cart = $this->resolveCart($request);

        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Session ID is required for guest carts.'], 400);
        }

        if ($request->filled('setup_id')) {
            return $this->addSetupToCart($request, $cart);
        }

        if ($request->filled('product_id')) {
            return $this->addProductToCart($request, $cart);
        }

        return response()->json([
            'status' => false,
            'message' => 'Either product_id or setup_id is required.',
        ], 422);
    }

    private function addProductToCart(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'sku_id' => 'nullable|exists:product_skus,sku_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $cart->items()
            ->where('product_id', $validated['product_id'])
            ->where('sku_id', $validated['sku_id'])
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'sku_id' => $validated['sku_id'] ?? null,
                'setup_id' => null,
                'quantity' => $validated['quantity'],
                'selections' => null,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Item added to cart successfully.',
        ]);
    }

    private function addSetupToCart(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'setup_id' => 'required|exists:setups,setup_id',
            'quantity' => 'required|integer|min:1',
            'choices' => 'nullable|array',
            'choices.*.group_name' => 'required_with:choices|string',
            'choices.*.setup_item_id' => 'required_with:choices|integer',
            'choices.*.product_id' => 'required_with:choices|integer',
            'choices.*.sku_id' => 'nullable|integer',
            'choices.*.label' => 'nullable|string',
        ]);

        $setup = Setup::findOrFail($validated['setup_id']);

        $availabilityError = $this->setupIsPurchasable($setup);
        if ($availabilityError) {
            return response()->json(['status' => false, 'message' => $availabilityError], 422);
        }

        [$selections, $choiceError] = $this->validateAndNormalizeSetupChoices(
            $setup,
            $validated['choices'] ?? null
        );

        if ($choiceError) {
            return response()->json(['status' => false, 'message' => $choiceError], 422);
        }

        $cartItem = $this->findMatchingSetupCartItem(
            $cart,
            (int) $validated['setup_id'],
            $selections
        );

        $newQuantity = $cartItem
            ? $cartItem->quantity + $validated['quantity']
            : $validated['quantity'];

        if ($setup->stock_quantity < $newQuantity) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock for this setup.',
            ], 422);
        }

        try {
            if ($cartItem) {
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cart->items()->create([
                    'setup_id' => $validated['setup_id'],
                    'product_id' => null, // Explicitly null for setups to prevent NOT NULL SQL crashes
                    'sku_id' => null,
                    'quantity' => $validated['quantity'],
                    'selections' => $selections,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'DB Error: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Setup added to cart successfully.',
        ]);
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($itemId);

        $cart = $this->resolveCart($request);
        if (!$cart || $cartItem->cart_id !== $cart->cart_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($cartItem->setup_id) {
            $setup = Setup::find($cartItem->setup_id);
            if (!$setup) {
                return response()->json(['status' => false, 'message' => 'Setup no longer exists.'], 422);
            }
            $availabilityError = $this->setupIsPurchasable($setup);
            if ($availabilityError) {
                return response()->json(['status' => false, 'message' => $availabilityError], 422);
            }
            if ($setup->stock_quantity < $validated['quantity']) {
                return response()->json(['status' => false, 'message' => 'Insufficient stock for this setup.'], 422);
            }
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => 'Quantity updated.',
        ]);
    }

    public function removeItem(Request $request, $itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);

        $cart = $this->resolveCart($request);
        if (!$cart || $cartItem->cart_id !== $cart->cart_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart.',
        ]);
    }
}