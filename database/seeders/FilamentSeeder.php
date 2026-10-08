<?php

namespace Database\Seeders;

use App\Models\Filament;
use Illuminate\Database\Seeder;

class FilamentSeeder extends Seeder
{
    public function run(): void
    {
        $filaments = [
            [
                'name' => 'PLA+ (عالي التحمل والتشطيب)',
                'spool_price' => 750.00,
                'spool_weight_grams' => 1000,
                'color_hint' => '#c8f228',
            ],
            [
                'name' => 'PLA قياسي (Standard)',
                'spool_price' => 675.00,
                'spool_weight_grams' => 1000,
                'color_hint' => '#edf2f1',
            ],
            [
                'name' => 'PETG (مقاوم للحرارة والرطوبة)',
                'spool_price' => 850.00,
                'spool_weight_grams' => 1000,
                'color_hint' => '#ef8950',
            ],
            [
                'name' => 'TPU مرن (Flexible Rubber)',
                'spool_price' => 1100.00,
                'spool_weight_grams' => 1000,
                'color_hint' => '#6ab8db',
            ],
        ];

        foreach ($filaments as $data) {
            $costPerGram = Filament::calculateGramCost($data['spool_price'], $data['spool_weight_grams']);
            Filament::updateOrCreate(
                ['name' => $data['name']],
                [
                    'spool_price' => $data['spool_price'],
                    'spool_weight_grams' => $data['spool_weight_grams'],
                    'cost_per_gram' => $costPerGram,
                    'color_hint' => $data['color_hint'],
                    'is_active' => true,
                ]
            );
        }
    }
}
