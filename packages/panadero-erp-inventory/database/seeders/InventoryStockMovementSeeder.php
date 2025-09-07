<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryStockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stockMovements = [
            [
                'item_id' => 1,
                'sku' => 'SKU-001',
                'movement_type' => 'in',
                'quantity' => 100,
                'reason' => 'Purchase Order',
                'reference' => 'PO-2025-001',
                'notes' => 'Initial stock from purchase order',
                'created_at' => now()->subDays(5),
            ],
            [
                'item_id' => 1,
                'sku' => 'SKU-001',
                'movement_type' => 'out',
                'quantity' => 25,
                'reason' => 'Sales Order',
                'reference' => 'SO-2025-001',
                'notes' => 'Sold to customer',
                'created_at' => now()->subDays(3),
            ],
            [
                'item_id' => 2,
                'sku' => 'SKU-002',
                'movement_type' => 'in',
                'quantity' => 50,
                'reason' => 'Purchase Order',
                'reference' => 'PO-2025-002',
                'notes' => 'Stock replenishment',
                'created_at' => now()->subDays(4),
            ],
            [
                'item_id' => 2,
                'sku' => 'SKU-002',
                'movement_type' => 'out',
                'quantity' => 15,
                'reason' => 'Production Use',
                'reference' => 'PROD-2025-001',
                'notes' => 'Used in production batch',
                'created_at' => now()->subDays(2),
            ],
            [
                'item_id' => 3,
                'sku' => 'SKU-003',
                'movement_type' => 'in',
                'quantity' => 20,
                'reason' => 'Purchase Order',
                'reference' => 'PO-2025-004',
                'notes' => 'Tools for maintenance',
                'created_at' => now()->subDays(1),
            ],
            [
                'item_id' => 4,
                'sku' => 'SKU-004',
                'movement_type' => 'in',
                'quantity' => 200,
                'reason' => 'Purchase Order',
                'reference' => 'PO-2025-002',
                'notes' => 'Raw materials for production',
                'created_at' => now()->subDays(4),
            ],
            [
                'item_id' => 4,
                'sku' => 'SKU-004',
                'movement_type' => 'out',
                'quantity' => 50,
                'reason' => 'Production Use',
                'reference' => 'PROD-2025-002',
                'notes' => 'Used in production batch',
                'created_at' => now()->subDays(1),
            ],
            [
                'item_id' => 5,
                'sku' => 'SKU-005',
                'movement_type' => 'in',
                'quantity' => 30,
                'reason' => 'Purchase Order',
                'reference' => 'PO-2025-004',
                'notes' => 'Accessories for tools',
                'created_at' => now()->subDays(1),
            ],
            [
                'item_id' => 5,
                'sku' => 'SKU-005',
                'movement_type' => 'out',
                'quantity' => 5,
                'reason' => 'Sales Order',
                'reference' => 'SO-2025-002',
                'notes' => 'Sold as accessory',
                'created_at' => now(),
            ],
            [
                'item_id' => 1,
                'sku' => 'SKU-001',
                'movement_type' => 'adjustment',
                'quantity' => 5,
                'reason' => 'Inventory Adjustment',
                'reference' => 'ADJ-2025-001',
                'notes' => 'Found additional stock during count',
                'created_at' => now()->subHours(2),
            ]
        ];

        DB::table('inventory_stock_movements')->insert($stockMovements);
    }
}
