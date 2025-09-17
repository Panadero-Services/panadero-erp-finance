<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_warehouses')->count() > 0) {
            $this->command->info('Inventory warehouses already exist, skipping...');
            return;
        }

        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'location' => '123 Industrial Blvd, Manufacturing District',
                'capacity' => 10000,
                'current_capacity' => 7500,
                'manager' => 'John Smith',
                'status' => 'active',
                'zones' => json_encode(['A', 'B', 'C', 'D']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Secondary Warehouse',
                'location' => '456 Storage St, Logistics Park',
                'capacity' => 5000,
                'current_capacity' => 3200,
                'manager' => 'Jane Doe',
                'status' => 'active',
                'zones' => json_encode(['E', 'F']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cold Storage Facility',
                'location' => '789 Refrigeration Ave, Cold Zone',
                'capacity' => 2000,
                'current_capacity' => 800,
                'manager' => 'Mike Johnson',
                'status' => 'active',
                'zones' => json_encode(['Cold-1', 'Cold-2']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maintenance Warehouse',
                'location' => '321 Service Rd, Maintenance Area',
                'capacity' => 1000,
                'current_capacity' => 450,
                'manager' => 'Sarah Wilson',
                'status' => 'active',
                'zones' => json_encode(['Maint-1']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Temporary Storage',
                'location' => '654 Temp St, Overflow Area',
                'capacity' => 3000,
                'current_capacity' => 0,
                'manager' => 'Tom Brown',
                'status' => 'inactive',
                'zones' => json_encode(['Temp-1', 'Temp-2']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('inventory_warehouses')->insert($warehouses);
    }
}
