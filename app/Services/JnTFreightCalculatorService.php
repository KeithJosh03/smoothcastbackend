<?php

namespace App\Services;

class JnTFreightCalculatorService
{
    /**
     * Map PSGC Region / Province or Island Group to J&T Geographic Distance Zone (Origin: Davao City)
     */
    public function resolveJnTZone(string $regionCode, ?string $provinceCode = null): string
    {
        $remoteProvinces = [
            'PALAWAN', 'BATANES', 'CATANDUANES', 'MARINDUQUE', 'MASBATE',
            'ROMBLON', 'TAWI-TAWI', 'TAWI_TAWI', 'BASILAN', 'SULU'
        ];

        if ($provinceCode) {
            $upperProv = strtoupper(str_replace([' ', '-'], '_', trim($provinceCode)));
            foreach ($remoteProvinces as $rem) {
                if (str_contains($upperProv, $rem)) {
                    return 'REMOTE_ISLAND';
                }
            }
        }

        $upperRegion = strtoupper(trim($regionCode));

        // Local Mindanao Region (Origin: Davao City)
        $mindanaoKeys = [
            'REG_09', 'REG_10', 'REG_11', 'REG_12', 'REG_13', 'BARMM',
            'MINDANAO', 'DAVAO', 'ZAMBOANGA', 'NORTHERN_MINDANAO', 'SOCCSKSARGEN', 'CARAGA'
        ];
        foreach ($mindanaoKeys as $mKey) {
            if (str_contains(str_replace([' ', '-'], '_', $upperRegion), $mKey)) {
                return 'INTRA_MINDANAO';
            }
        }

        // Visayas Inter-Island
        $visayasKeys = [
            'REG_06', 'REG_07', 'REG_08',
            'VISAYAS', 'WESTERN_VISAYAS', 'CENTRAL_VISAYAS', 'EASTERN_VISAYAS'
        ];
        foreach ($visayasKeys as $vKey) {
            if (str_contains(str_replace([' ', '-'], '_', $upperRegion), $vKey)) {
                return 'VISAYAS';
            }
        }

        // Luzon & Metro Manila (Fallback default)
        return 'LUZON_NCR';
    }

    /**
     * Base Freight Rate Matrix based on Distance Zone + Chargeable Weight Tier (Origin: Davao City)
     */
    public function getJnTBaseRate(string $zone, float $chargeableWeightKg): float
    {
        $rateMatrix = [
            'INTRA_MINDANAO' => ['BASE_500G' => 85.00,  'TIER_1KG_3KG' => 180.00, 'PER_KG_EXCESS' => 45.00],
            'VISAYAS'        => ['BASE_500G' => 105.00, 'TIER_1KG_3KG' => 195.00, 'PER_KG_EXCESS' => 50.00],
            'LUZON_NCR'      => ['BASE_500G' => 105.00, 'TIER_1KG_3KG' => 215.00, 'PER_KG_EXCESS' => 55.00],
            'REMOTE_ISLAND'  => ['BASE_500G' => 115.00, 'TIER_1KG_3KG' => 230.00, 'PER_KG_EXCESS' => 60.00],
        ];

        $rates = $rateMatrix[$zone] ?? $rateMatrix['LUZON_NCR'];

        if ($chargeableWeightKg <= 0.5) {
            return (float) $rates['BASE_500G'];
        }

        if ($chargeableWeightKg <= 3.0) {
            return (float) $rates['TIER_1KG_3KG'];
        }

        $excessKg = ceil($chargeableWeightKg - 3.0);
        return (float) ($rates['TIER_1KG_3KG'] + ($excessKg * $rates['PER_KG_EXCESS']));
    }

    /**
     * Entry Point for Cart & Checkout Freight Calculation
     *
     * @param iterable $cartItems Collection or array of cart item models
     * @param string $regionCode Region code or island group (e.g. REG_11 or MINDANAO)
     * @param string|null $provinceCode Province name or code (e.g. DAVAO DEL SUR)
     * @return array Detailed J&T shipping metrics
     */
    public function calculateShipping(iterable $cartItems, string $regionCode, ?string $provinceCode = null): array
    {
        $presets = config('jnt.presets') ?: [
            'POUCH_S'     => ['length' => 20, 'width' => 15, 'height' => 5, 'is_bulky' => false],
            'POUCH_L'     => ['length' => 30, 'width' => 20, 'height' => 10, 'is_bulky' => false],
            'BOX_S'       => ['length' => 30, 'width' => 24, 'height' => 15, 'is_bulky' => false],
            'BOX_M'       => ['length' => 36, 'width' => 27, 'height' => 18, 'is_bulky' => false],
            'BOX_L'       => ['length' => 46, 'width' => 30, 'height' => 23, 'is_bulky' => true],
            'LONG_PARCEL' => ['length' => 180, 'width' => 10, 'height' => 10, 'is_bulky' => true],
        ];

        $divisor = config('jnt.volumetric_divisor') ?: 3500;
        $configuredBulkyFee = config('jnt.bulky_surcharge') ?: 150.00;
        $insuranceThreshold = config('jnt.insurance_threshold') ?: 2000.00;
        $insuranceRate = config('jnt.insurance_rate') ?: 0.01;

        $totalPhysicalGrams = 0;
        $totalVolumetricGrams = 0;
        $hasLongParcel = false;
        $grossDeclaredValue = 0.0;

        foreach ($cartItems as $item) {
            $qty = (int) ($item->quantity ?? 1);

            if (!empty($item->setup_id) && !empty($item->setup)) {
                // Setup Bundle item
                $setup = $item->setup;
                $price = (float) $setup->bundle_price;
                $grossDeclaredValue += ($price * $qty);

                $setupPhysicalGrams = 0;
                $setupVolumetricGrams = 0;
                $setupIsBulky = false;

                if (is_array($item->selections)) {
                    foreach ($item->selections as $sel) {
                        $pWeight = 0;
                        $pPkg = 'POUCH_S';
                        $pBulky = false;

                        if (isset($sel['sku_id']) && $sel['sku_id']) {
                            $s = \App\Models\ProductSku::find($sel['sku_id']);
                            if ($s) {
                                $pWeight = $s->weight_g ?? $s->weight ?? 0;
                                $pPkg = $s->packaging_type ?? 'POUCH_S';
                                $pBulky = $s->effective_is_bulky;
                            }
                        } elseif (isset($sel['product_id']) && $sel['product_id']) {
                            $p = \App\Models\Product::find($sel['product_id']);
                            if ($p) {
                                $pWeight = $p->weight_g ?? $p->weight ?? 0;
                                $pPkg = $p->packaging_type ?? 'POUCH_S';
                                $pBulky = in_array($pPkg, ['BOX_L', 'LONG_PARCEL']) || ($p->is_bulky ?? false);
                            }
                        }

                        $setupPhysicalGrams += $pWeight;
                        $preset = $presets[$pPkg] ?? $presets['POUCH_S'];
                        $volKg = ($preset['length'] * $preset['width'] * $preset['height']) / $divisor;
                        $setupVolumetricGrams += ($volKg * 1000);

                        if ($pBulky || in_array($pPkg, ['BOX_L', 'LONG_PARCEL'])) {
                            $setupIsBulky = true;
                        }
                    }
                }

                if ($setupPhysicalGrams === 0) {
                    $setupPhysicalGrams = $setup->effective_weight ?? $setup->weight ?? 500;
                    $preset = $presets['BOX_M'];
                    $setupVolumetricGrams = (($preset['length'] * $preset['width'] * $preset['height']) / $divisor) * 1000;
                }

                $totalPhysicalGrams += ($setupPhysicalGrams * $qty);
                $totalVolumetricGrams += ($setupVolumetricGrams * $qty);
                if ($setupIsBulky || $setup->is_bulky) {
                    $hasLongParcel = true;
                }

            } else {
                // Product / Variant SKU line
                $sku = $item->sku;
                $product = $item->product;

                $packagingType = $sku->packaging_type
                    ?? $product->packaging_type
                    ?? 'POUCH_S';

                $preset = $presets[$packagingType] ?? $presets['POUCH_S'];

                // Physical Weight (Grams)
                $physicalWeight = $sku->weight_g
                    ?? $sku->weight
                    ?? $product->weight_g
                    ?? $product->weight
                    ?? 0;

                $totalPhysicalGrams += ($physicalWeight * $qty);

                // Volumetric calculation via preset dimensions (Divisor 3500)
                $itemVolumetricKg = ($preset['length'] * $preset['width'] * $preset['height']) / $divisor;
                $totalVolumetricGrams += ($itemVolumetricKg * 1000 * $qty);

                // Bulky / Long parcel check
                $isItemBulky = in_array($packagingType, ['BOX_L', 'LONG_PARCEL'])
                    || ($preset['is_bulky'] ?? false)
                    || ($sku ? $sku->effective_is_bulky : ($product->is_bulky ?? false));

                if ($isItemBulky) {
                    $hasLongParcel = true;
                }

                // Final price for insurance
                $unitPrice = $sku ? $sku->final_price : ($product->final_price ?? $product->base_price ?? 0);
                $grossDeclaredValue += ($unitPrice * $qty);
            }
        }

        // 1. Determine Chargeable Weight: MAX(Physical, Volumetric)
        $chargeableWeightKg = max(
            0.1,
            max($totalPhysicalGrams, $totalVolumetricGrams) / 1000
        );

        // 2. Resolve Geographic Distance Zone
        $distanceZone = $this->resolveJnTZone($regionCode, $provinceCode);

        // 3. Base Shipping Fee
        $baseFreight = $this->getJnTBaseRate($distanceZone, $chargeableWeightKg);

        // 4. Oversized Handling / Long Tube Fee (₱150 for Long Parcel / Bulky)
        $bulkySurcharge = $hasLongParcel ? (float) $configuredBulkyFee : 0.00;

        // 5. Declared Value Insurance Fee (1% for orders > ₱2,000)
        $insuranceFee = $grossDeclaredValue > $insuranceThreshold
            ? round($grossDeclaredValue * $insuranceRate, 2)
            : 0.00;

        $totalShippingFee = $baseFreight + $bulkySurcharge + $insuranceFee;

        return [
            'distance_zone'        => $distanceZone,
            'chargeable_weight_kg' => round($chargeableWeightKg, 2),
            'physical_weight_kg'   => round($totalPhysicalGrams / 1000, 2),
            'physical_weight_g'    => (int) $totalPhysicalGrams,
            'volumetric_weight_kg' => round($totalVolumetricGrams / 1000, 2),
            'base_freight'         => (float) $baseFreight,
            'bulky_surcharge'      => (float) $bulkySurcharge,
            'insurance_fee'        => (float) $insuranceFee,
            'total_shipping_fee'   => (float) $totalShippingFee,
            'has_bulky_item'       => (bool) $hasLongParcel,
            'package_type'         => $hasLongParcel ? 'Special Courier / PVC Rod Tube' : 'Standard Parcel Box',
        ];
    }
}
