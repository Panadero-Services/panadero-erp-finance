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
            // Core inventory data
            InventoryItemSeeder::class,
            InventoryWarehouseSeeder::class,
            InventorySupplierSeeder::class,
            InventoryPurchaseOrderSeeder::class,
            InventoryStockItemSeeder::class,
            InventoryStockMovementSeeder::class,
            
            // New comprehensive inventory data
            InventoryProductsSeeder::class,
            InventoryStockLevelsSeeder::class,
            InventoryPartnersSeeder::class, // Must come before orders
            InventoryIncomingOrdersSeeder::class,
            InventoryOutgoingOrdersSeeder::class,
            InventoryCloseoutsSeeder::class,
        ]);
    }
}
