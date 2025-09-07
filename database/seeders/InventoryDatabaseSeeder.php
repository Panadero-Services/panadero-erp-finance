<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            InventoryItemSeeder::class,
            InventoryWarehouseSeeder::class,
            InventorySupplierSeeder::class,
            InventoryPurchaseOrderSeeder::class,
            InventoryStockMovementSeeder::class,
        ]);
    }
}
