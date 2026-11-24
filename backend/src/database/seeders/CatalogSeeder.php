<?php

namespace Database\Seeders;

use App\Models\JokiCategory;
use App\Models\JokiPackage;
use Illuminate\Database\Seeder;

/**
 * Katalog awal, sama dengan data tiruan di aplikasi Mobile. Aman dijalankan berulang.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'Joki Explore Map',
                'slug' => 'joki-explore-map',
                'description' => 'Eksplorasi peta 100%: peti, teleport, dan Oculus.',
                'packages' => [
                    ['Mondstadt 100%', 75000, 36],
                    ['Liyue 100%', 120000, 60],
                    ['Inazuma 100%', 150000, 72],
                    ['Sumeru 100%', 210000, 96],
                    ['Fontaine 100%', 175000, 60],
                    ['Natlan 100%', 185000, 72],
                    ['Nod-Krai 100%', 165000, 60],
                    ['Snezhnaya 100%', 220000, 96],
                ],
            ],
            [
                'name' => 'Joki Satuan',
                'slug' => 'joki-satuan',
                'description' => 'Joki konten satuan seperti Spiral Abyss.',
                'packages' => [
                    ['Spiral Abyss Floor 9-12', 45000, 12],
                ],
            ],
            [
                'name' => 'Joki Quest',
                'slug' => 'joki-quest',
                'description' => 'Penyelesaian quest cerita dan Archon.',
                'packages' => [
                    ['Quest Archon Fontaine', 60000, 24],
                ],
            ],
        ];

        foreach ($catalog as $item) {
            $category = JokiCategory::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );

            foreach ($item['packages'] as [$name, $price, $hours]) {
                JokiPackage::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $name],
                    [
                        'price' => $price,
                        'estimated_hours' => $hours,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
