<?php

return [
    /*
    |--------------------------------------------------------------------------
    | J&T Express Packaging Presets
    |--------------------------------------------------------------------------
    |
    | Centralized packaging dimensions (in cm) for volumetric weight calculations
    | Formula: Volumetric Weight (kg) = (Length x Width x Height) / 3500
    |
    */
    'presets' => [
        'POUCH_S' => [
            'code' => 'POUCH_S',
            'label' => 'Pouch S (Small Accessories / Lures)',
            'icon' => '✉️',
            'length' => 20,
            'width' => 15,
            'height' => 5,
            'is_bulky' => false,
            'description' => 'Lures, hooks, small line spools',
        ],
        'POUCH_L' => [
            'code' => 'POUCH_L',
            'label' => 'Pouch L (Reels & Accessories)',
            'icon' => '✉️',
            'length' => 30,
            'width' => 20,
            'height' => 10,
            'is_bulky' => false,
            'description' => 'Reels, small tackle accessories',
        ],
        'BOX_S' => [
            'code' => 'BOX_S',
            'label' => 'Box S (Standard Tackle Box)',
            'icon' => '📦',
            'length' => 30,
            'width' => 24,
            'height' => 15,
            'is_bulky' => false,
            'description' => 'Boxed reels (e.g., Daiwa Exist)',
        ],
        'BOX_M' => [
            'code' => 'BOX_M',
            'label' => 'Box M (Medium Tackle Case)',
            'icon' => '📦',
            'length' => 36,
            'width' => 27,
            'height' => 18,
            'is_bulky' => false,
            'description' => 'Multiple reels, medium utility cases',
        ],
        'BOX_L' => [
            'code' => 'BOX_L',
            'label' => 'Box L (Large Storage / Mebao Box)',
            'icon' => '📦',
            'length' => 46,
            'width' => 30,
            'height' => 23,
            'is_bulky' => true,
            'description' => 'Heavy utility storage cases (e.g., Mebao Box) [Bulky]',
        ],
        'LONG_PARCEL' => [
            'code' => 'LONG_PARCEL',
            'label' => 'Long Parcel / Tube (Fishing Rods)',
            'icon' => '🎣',
            'length' => 180,
            'width' => 10,
            'height' => 10,
            'is_bulky' => true,
            'description' => 'Fishing Rods / Heavy-Duty PVC Tubes [Bulky]',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Surcharges & Rates
    |--------------------------------------------------------------------------
    */
    'volumetric_divisor' => 3500,
    'bulky_surcharge' => 150.00,
    'insurance_threshold' => 2000.00,
    'insurance_rate' => 0.01,
];
