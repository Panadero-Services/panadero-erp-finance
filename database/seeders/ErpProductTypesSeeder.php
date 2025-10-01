<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ErpProductType;

class ErpProductTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            [
                'name' => 'normal',
                'description' => 'Standard discrete items that are counted individually'
            ],
            [
                'name' => 'bulk',
                'description' => 'Bulk materials measured by weight or volume'
            ],
            [
                'name' => 'liquid',
                'description' => 'Liquid materials measured by volume'
            ],
            [
                'name' => 'service',
                'description' => 'Services or intangible products'
            ],
            [
                'name' => 'digital',
                'description' => 'Digital products or software'
            ],
            [
                'name' => 'raw_material',
                'description' => 'Raw materials used in production'
            ],
            [
                'name' => 'finished_good',
                'description' => 'Completed products ready for sale'
            ],
            [
                'name' => 'component',
                'description' => 'Components used in assembly'
            ],
            [
                'name' => 'tool',
                'description' => 'Tools and equipment'
            ],
            [
                'name' => 'consumable',
                'description' => 'Items that are consumed during operations'
            ]
        ];

        foreach ($productTypes as $type) {
            ErpProductType::create($type);
        }

        $this->command->info('Product types seeded successfully!');
        $this->command->info('Total product types: ' . ErpProductType::count());
    }
}