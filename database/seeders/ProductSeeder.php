<?php

namespace Database\Seeders;

use App\Models\Filament;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $defaultFilament = Filament::first();
        $gramCost = $defaultFilament ? (float) $defaultFilament->cost_per_gram : 0.75;

        foreach ([
            [
                'name' => 'Orbit Stand',
                'description' => 'حامل موبايل هندسي قابل للتعديل مكتبي',
                'weight' => 90,
                'multiplier' => 4,
                'color_options' => ['برتقالي', 'أسود غير لامع', 'أخضر ليموني'],
                'color_label' => 'لون الحامل',
            ],
            [
                'name' => 'Loop Dock',
                'description' => 'منظّم كابلات ومكتب ثلاثي الأبعاد متين',
                'weight' => 115,
                'multiplier' => 4,
                'color_options' => ['أسود مطفي', 'أبيض ثلجي', 'رمادي معدني'],
                'color_label' => 'لون المنظم',
            ],
            [
                'name' => 'Stack Planter',
                'description' => 'أصيص نباتات مكتبي مفرغ ثلاثي الطبقات',
                'weight' => 104,
                'multiplier' => 4,
                'color_options' => ['قاعدة سوداء / جسم ليموني', 'رمادي رخامي', 'أخضر زيتوني'],
                'color_label' => 'تخصيص لون القطعة والقاعدة',
            ],
        ] as $sort => $product) {
            $weight = (float) $product['weight'];
            $multiplier = (float) $product['multiplier'];
            $basePrice = Product::basePrice($weight, $gramCost);
            $price = Product::calculatedPrice($weight, $multiplier, $gramCost);

            Product::updateOrCreate(['name' => $product['name']], [
                'description' => $product['description'],
                'filament_id' => $defaultFilament?->id,
                'weight_grams' => $weight,
                'base_price' => $basePrice,
                'multiplier' => $multiplier,
                'price' => $price,
                'color_options' => $product['color_options'],
                'color_label' => $product['color_label'],
                'sort_order' => $sort,
                'is_active' => true,
            ]);
        }
    }
}
