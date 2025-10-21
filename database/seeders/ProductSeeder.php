<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder {

    public function run(): void  {
        DB::table('products')->insert([
            [
            'brand_id' => 1 ,
            'category_id' => 1,
            'type_id' => 1, 
            'product_name' => 'Daiwa 18 EXIST G LT 3000 CXH',
            'base_price' => 27000.50,
            'features' => 'ATD drag technology reinforced by 30%, full-size drag button for stability
Bearings: 12 + 1 including 2 Mag Sealed and 10 CRBBs
Monocoque magnesium body and ZAION Air Rotor
Ultralight Aluminium Air Spool
CrossWrap line lay
Ultralight one-piece unscrewable and slit Air Handle in aluminium
Supplied in a neoprene sleeve with mesh sides',
            'specifications' => null,
            'description' =>  "DAIWA has held the same dream for 60 years. To bring to the world, the finest designed reel ever seen. A reel that is so light and easy to use that it feels like an extension of your body. A reel that is so strong that it will withstand the toughest conditions an angler and the environment can throw at it. The dream to create a reel that turns the contradiction of being LIGHT yet TOUGH on its head.

Now, after a long history of innovation, and endless challenges, that dream has become a reality. 2018 NEW EXIST. Here, for the anglers of the world, is a reel like we've never seen before. A reel designed for ultimate performance, joy and angling perfection."
            ],
            [                                                     
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 10600.50,
            'product_name' => '18 BLAST LT5000D-CXH',
            'specifications' => 'Winding length (cm / one rotation of handle): 105
Gear ratio: 6.2
Own weight (g): 285
Maximum drag force (kg): 12
PE (No.-m): 2.5-300, 3-210
Bearing (ball / roller): 6/1',
            'features' => null,
            'description' => null,
            ],
            [
            'brand_id' => 1,
            'category_id' => 2,
            'type_id' => 2,
            'product_name' => "CB NAGAMASA 115GM COL 04",
            'base_price' => 700.00,
            'specifications' => 'SMITH CB NAGAMASA
            Length: 185MM
            Weight: 115GM
            Color: COL 04',
            'features' => null,
            'description' => "The CB. Nagamasa has a long flat body, center balance, and laser edge back design. The more you release the line tension when falling, the more the lure will receive water on a long flat surface, and the longer the horizontal slide will be due to the center balance, appealing to fish."
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 4,
            'base_price' => 50750.00,
            'product_name' => "Daiwa Seaborg 500MJ-AT",
            'specifications' => null,
            'features' => '-Japan Domestic Model.
-Gear Ratio: 3.7
-Winding length (cm / handle 1 rotation): 57
-Weight - (g): 1010
-Max Drag (kg): 23
-Line Capacity PE (No.-m): 4-500-5-400-6-300
-Line Capacity nylon (No.-m): 6-350-7-300-8-250
-Bearings: 22/1
-Max Winding Power (kg): 90 (100)
-Normal hoisting speed at 1kg load (m/min): Hi180(200)-Lo125(140)
-JAFS-Standard hoisting force-(kg): 22
-JAFS standard speed (m/min): Hi230-Lo150',
            'description' => 'Mega Power & Mega Speed “AUTOMATIC MEGA MONSTER”
Aluminum JOG / NEW Fukase Clutch System
Mega Power & Mega Speed Electric with Automatic Shelf Stop Function on Seaborg 500MJ Base
Flagship model that supports a wide range of targets, from amberjack yellowtail blue-backed fish, dried squid multi-point hanging, mid-deep root fish to amberjack fukase amberjack
At the heart is a MAGMAX motor that has both torque and instantaneous power.
Tough setting with excellent heat dissipation and durability by applying aluminum member to the motor housing
At the same time, MEGA TWIN-PRO is used to comfortably switch power and speed gears without shift shock.
In addition, the tough drag ATD that keeps the blue-backed fish running smoothly and keeps working smoothly makes it safe to handle unexpected big fish.
In terms of operability, a one-handed comfortable operation aluminum JOG power lever that does not give up the initiative in instant winding is adopted.
In addition, the fall brake dial that can set the fall speed instantly has a drop bait, which is an excellent one that captures the middle layer reaction of Japanese flying squid.
In terms of durability, waterproof and durable technology MAGSEA LED ball bearings maintain initial performance
In addition, a large dot liquid crystal display that looks clear even when using polarized glass is used for the counter section.
Newly added “NEW Fukase Clutch System” and “Automatic Shelf Stop Function”
Anniversary AUTOMATIC MEGA MONSTER for all targets'
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 7800.00,
            'product_name' => "19 LEXA LT5000D-CXH",
            'specifications' => 'Line Retrieve: 105
Gear Ratio: 6.2
Max Drag: 12
Weight: 290
Braid Cap: #2-350, #2.5-300, #3-210
Bearing: 5 / 1',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 43000.00,
            'product_name' => "20 SALTIGA (G) 8000P",
            'specifications' => '20 SALTIGA (G) 8000P
Bearings : 12BB (6CRBB-2MSBB)
Capacity PE: PE4 - 300m
Max Drag: 25kg
Gear: 4.8
Pickup: 92cm
Weight: 645g',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 4380.00,
            'product_name' => "20 TAMS LT2500",
            'specifications' => '20 TAMS LT2500
Max Drag : 10kg
Braid Line Capacity : (#PE/m): #0.8/200
Gear Ratio : 5.3:1
Water : Salt Water
Ball Bearing : 6BB + 1RB
Line Per Handle Turn : 75cm
Weight : 230g',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 4600.00,
            'product_name' => "20 TAMS LT4000-C",
            'specifications' => '20 TAMS LT4000-C
Max Drag : 10kg
Braid Line Capacity : (#PE/m): #1.5/200
Gear Ratio : 5.2:1
Water : Salt Water
Ball Bearing : 6BB + 1RB
Line Per Handle Turn : 82cm
Weight : 275g',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 1,
            'base_price' => 4200.00,
            'product_name' => "22Daiwa PT 150H BK",
            'specifications' => null,
            'features' => '110m Baitcast Handle
Aluminium Baitcast Handle
Aluminium Body
EVA Knob – Round Ball Knob
Infinite Anti-Reverse
Magnetic Cast Control
MODEL PT 150H BK
GEAR 7.3
DRAG PRESSURE 6kg
WEIGHT 210g
BALL BEARINGS 4BB 1RB
SPOOL CAPACITY PE 2 - 200m',
            'description' => 'Built off a durable aluminium body and sporting an extra-long 110mm handle with power EVA knobs, the PT150H is a perfect candidate for these hard fighting fish.
Featuring Daiwa’s MAGFORCE magnetic cast control, the PT150H will cast long and trouble-free, providing confidence inspiring performance. A 7.3:1 fast gear ratio is perfect for cast & retrieve lure fishing for our most iconic species.'
            ],
            [
            'brand_id' => 3,
            'category_id' => 2,
            'type_id' => 6,
            'base_price' => 280.00,
            'product_name' => "Bait Breath AJ-R Ajing Reversible (8/25)",
            'specifications' => 'Number of pieces: 10 pcs
List price: 280pesos
Size: 2 inches
Total length: 5.4cm
Hook set body size (body): 2.2cm
Appeal parts size (touch): 3.2cm
Target fish: horse mackerel, grouper, and other light game target fish
Recommended Rigs: Jig Head #6, #8, Carolina Rig, Downshot Rig, Spoon Trailer, etc.

',
            'features' => null,
            'description' => 'AJ-R 2inch
Development name "Sokugami-SOKU GAMI" is the design of the Omoto of AJ-R
The AJ-R series, it is the first basic item of the series that debuted from this 2 inches.
A design that combines explosive fishing results and good return, durability, and ease of attachment.
It was sent to the world as an item aiming to achieve a bundle, and the excellent delicate part design is of course a good response!
Recommended items for Azing fans!!'
            ],
            [
            'brand_id' => 2,
            'category_id' => 2,
            'type_id' => 5,
            'base_price' => 1850.00,
            'product_name' => "ACUP HAMMER HEAD 145MM/54G FLOATING COL 05",
            'specifications' => 'Type: Floating
Length: 145mm
Weight: 54gm
Color: COL 05',
            'features' => null,
            'description' => 'Smith hammerhead A cup popper
Developed in collaboration with Hammerhead, the horizontal lifting Popper masterpiece "A Dance foam cup."
The famous SMITH / Hammerhead Popper is perfect for destination fisherman targeting Tuna, Dolphin Fish, Wahoo, Mahi Mahi, Spanish Mackerel and other big game species.
Made of wood this 14.5 cm Popper weighs in at 54g and makes a load of splash on entry. High levels of enticing noise continues to be produced especially with a hard jerking motion bringing on the most spectacular of strikes!'
            ],
            [
            'brand_id' => 4,
            'category_id' => 2,
            'type_id' => 6,
            'base_price' => 290.00,
            'product_name' => "Gekkabijin Beam Stick Kiwami 2.2",
            'specifications' => null,
            'features' => 'Features of the Kiwami series
By adding Amino X to the outside of the special concentrated shrimp powder core, the fish gathering power of the beam stick is further enhanced!
2.2 inch long
10pcs per pack',
            'description' => 'Long-selling worm Gekkabijin Beam Stick with Amino X is now available.
■Added an ultimate model containing Amino X to the popular long-selling worm, beam stick
By adding Amino X to the outside of the special concentrated shrimp powder core, the fish gathering power of the beam stick is further enhanced!
■The hardness of the worm material is a little milder than the existing beamstick.
The existing beamsticks use a strong material, but the extreme is a little mild. By softening the material slightly to the extent that the goodness of the beam stick is not lost, the bite is better, and it is also easier to set to the jig head.

Contains Amino X
"Amino X" raises the activity of fish and attracts it intensely. The secret lies in the umami ingredients. Umami ingredients are glutamic acid, alanine, aspartic acid, etc., which are classified as amino acids, and "Amino X" is condensed.'
            ],
            [
            'brand_id' => 2,
            'category_id' => 3,
            'type_id' => 7,
            'base_price' => 18000.00,
            'product_name' => "AOF K - FIBRE 76H",
            'specifications' => 'Smith x Aof Fibre 76h popping rod
Length: 7ft6in
Section: 2 pc
Lure Weight: 50-120g
Line: pe 6-7
Max Drag: 8kg',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 5,
            'category_id' => 3,
            'type_id' => 8,
            'base_price' => 7200.00,
            'product_name' => "BLACK EXPERT II SLOW JIGGING EBESJ II-B602MH",
            'specifications' => 'ECODA BLACK EXPERT II SLOW JIGGING
EBESJ II - B602MH Overhead Rod
Length: 6ft
Jig Max: 400g
Line: PE 2-4
Section: 2
Test: 18kg',
            'features' => null,
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 3,
            'type_id' => 9,
            'base_price' => 5800.00,
            'product_name' => "Daiwa Blast SLJ AP 63LS-S (Super Light Jigging JDM)",
            'specifications' => 'Rod Length: 1.91m
Number of Pieces: 2
Folded Length: 100cm
Rod Weight: 102g
Rod Power: Light
Line Weight: PE 0.4-0.8
Jig Weight: 10-45g
Fuji guides',
            'features' => null,
            'description' => 'Spinning Model
Recommended Reel Size LT 3000 - LT 5000
Sensitive Super Light Jiging Rod. Targets such as grunt, gurnard, and root fish. A model aimed at improving the operability and sensitivity of lightweight jigs in shallow areas with a water depth of about 10 to 30 m. In particular, if you do not catch the delicate atari in the fall such as grunt, the sensitivity of the rod and the goodness of biting will greatly affect the fishing result for the target that can not increase the number. A mega top is used for the top of this model to achieve high sensitivity and good bite unique to solids. In addition, a V-joint structure is used for the joint part of the blank reinforced with braiding X, which is a tough material HVF. Even with the center cut 2-piece specification, the strength and bending equivalent to 1 piece can be achieved, so even if it is a super light jigging rod, it can be used with confidence.'
            ],
            [
            'brand_id' => 1,
            'category_id' => 3,
            'type_id' => 7,
            'base_price' => 4000.00,
            'product_name' => "Daiwa Lurenist 74ul-s rod",
            'specifications' => '■Total length (m): 2.24
■Number of sections : 2
■Finish (cm): 117
■Ow/Wed weight (g): 119
■Lure weight (g): 1-6
■Compatible nylon line (lb): 2-6',
            'features' => null,
            'description' => 'The Daiwa Lurenist 74UL-S target fish is horse mackerel, rockfish
It is a lure rod with a supple solid tip that fish dont notice when taking the bait. It is designed best for distance throwing small lures, and it is ideal for rockfishing and ajing.
'
            ],
            [
            'brand_id' => 8,
            'category_id' => 4,
            'type_id' => 10,
            'base_price' => 180.00,
            'product_name' => "Decoy Bullet Sinker",
            'specifications' => null,
            'features' => null,
            'description' => "A standard bullet sinker that matches basic rigs such as Texas rigs and Carolina rigs. A protection tube is attached to the through the part to prevent line breaks. Even if it looks normal, we pay attention to the details!"
            ],
            [
            'brand_id' => 9,
            'category_id' => 4,
            'type_id' => 11,
            'base_price' => 65.00,
            'product_name' => "Mebao Accessory Box MBE1",
            'specifications' => null,
            'features' => null,
            'description' => "A multifunctional Bait and Accessory Box Made of thickened PP material that comes in 3 different configuration for the anglers preference. Its has a large capacity size of 175 x 105 x 36mm hat is very convenient to place on backpacks or tackle boxes."
            ],
            [
            'brand_id' => 10,
            'category_id' => 2,
            'type_id' => 2,
            'base_price' => 210.00,
            'product_name' => "Tukob Nomadic Piercer Ford Darting",
            'specifications' => null,
            'features' => null,
            'description' => null
            ],
        ]);
    }
}



