<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ErpBusinessDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏢 Seeding comprehensive ERP business data...');

        // Seed suppliers
        $this->seedSuppliers();
        
        // Seed sites
        $this->seedSites();
        
        // Seed products
        $this->seedProducts();
        
        // Seed orders
        $this->seedOrders();
        
        // Seed order lines
        $this->seedOrderLines();

        $this->command->info('✅ ERP business data seeded successfully!');
    }

    private function seedSuppliers(): void
    {
        $this->command->info('🏭 Seeding suppliers...');
        
        $suppliers = [
            [
                'name' => 'Industrial Supply Co',
                'identifier' => 'IND001',
                'comment' => 'Primary industrial supplier',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['category' => 'Industrial', 'rating' => 'A']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Components Ltd',
                'identifier' => 'TECH01',
                'comment' => 'Electronic components supplier',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['category' => 'Electronics', 'rating' => 'B']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Raw Materials Inc',
                'identifier' => 'RAW001',
                'comment' => 'Raw materials supplier',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['category' => 'Raw Materials', 'rating' => 'A']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->updateOrInsert(
                ['identifier' => $supplier['identifier']],
                $supplier
            );
        }
    }

    private function seedSites(): void
    {
        $this->command->info('🏢 Seeding sites...');
        
        $sites = [
            [
                'name' => 'Main Warehouse',
                'identifier' => 'MAIN01',
                'comment' => 'Primary distribution center',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['type' => 'Warehouse', 'capacity' => '10000']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Production Facility',
                'identifier' => 'PROD01',
                'comment' => 'Main production site',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['type' => 'Production', 'capacity' => '5000']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Complex',
                'identifier' => 'OFF01',
                'comment' => 'Administrative headquarters',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['type' => 'Office', 'capacity' => '500']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($sites as $site) {
            DB::table('erp_sites')->updateOrInsert(
                ['identifier' => $site['identifier']],
                $site
            );
        }
    }

    private function seedProducts(): void
    {
        $this->command->info('📦 Seeding products...');
        
        // Get unit IDs
        $unitIds = DB::table('units')->pluck('id', 'symbol')->toArray();
        
        $products = [
            [
                'name' => 'Steel Rods',
                'identifier' => 'STEEL001',
                'product_type' => 'Bulk',
                'unit_id' => $unitIds['kg'] ?? 1,
                'comment' => 'High-grade steel rods',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['material' => 'Steel', 'grade' => 'A36']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Electronic Components',
                'identifier' => 'ELEC001',
                'product_type' => 'Article',
                'unit_id' => $unitIds['pcs'] ?? 1,
                'comment' => 'Standard electronic components',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['category' => 'Electronics', 'voltage' => '12V']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consulting Services',
                'identifier' => 'CONS001',
                'product_type' => 'Service',
                'unit_id' => $unitIds['pcs'] ?? 1,
                'comment' => 'Professional consulting services',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['category' => 'Services', 'rate' => 'hourly']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['identifier' => $product['identifier']],
                $product
            );
        }
    }

    private function seedOrders(): void
    {
        $this->command->info('📋 Seeding orders...');
        
        $orders = [
            [
                'order_number' => 'ORD-2025-001',
                'json' => json_encode(['customer_name' => 'Acme Corporation', 'priority' => 'high']),
                'order_type' => 'sale',
                'order_date' => now()->subDays(5),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => 'ORD-2025-002',
                'json' => json_encode(['customer_name' => 'Tech Solutions Ltd', 'priority' => 'medium']),
                'order_type' => 'sale',
                'order_date' => now()->subDays(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => 'RENT-2025-001',
                'json' => json_encode(['customer_name' => 'Global Industries', 'priority' => 'high']),
                'order_type' => 'rental',
                'order_date' => now()->subDays(1),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($orders as $order) {
            DB::table('orders')->updateOrInsert(
                ['order_number' => $order['order_number']],
                $order
            );
        }
    }

    private function seedOrderLines(): void
    {
        $this->command->info('📝 Seeding order lines...');
        
        // First, create an article group if it doesn't exist
        $this->command->info('📁 Creating article group...');
        $articleGroupId = DB::table('article_groups')->insertGetId([
            'name' => 'General Products',
            'json' => json_encode(['description' => 'General product category', 'type' => 'mixed']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Get order IDs
        $orderIds = DB::table('orders')->pluck('id', 'order_number')->toArray();
        
        // Create articles
        $articleIds = [];
        $articles = [
            [
                'name' => 'Steel Rods',
                'description' => 'High-grade steel rods for construction',
                'json' => json_encode(['material' => 'Steel', 'grade' => 'A36', 'identifier' => 'STEEL001']),
                'is_for_sale' => true,
                'is_for_rent' => false,
                'is_active' => true,
                'article_group_id' => $articleGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Electronic Components',
                'description' => 'Standard electronic components',
                'json' => json_encode(['category' => 'Electronics', 'voltage' => '12V', 'identifier' => 'ELEC001']),
                'is_for_sale' => true,
                'is_for_rent' => false,
                'is_active' => true,
                'article_group_id' => $articleGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consulting Services',
                'description' => 'Professional consulting services',
                'json' => json_encode(['category' => 'Services', 'rate' => 'hourly', 'identifier' => 'CONS001']),
                'is_for_sale' => false,
                'is_for_rent' => true,
                'is_active' => true,
                'article_group_id' => $articleGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($articles as $article) {
            $insertedId = DB::table('articles')->insertGetId($article);
            $articleIds[] = $insertedId;
        }
        
        $orderLines = [
            [
                'order_id' => $orderIds['ORD-2025-001'] ?? 1,
                'article_id' => $articleIds[0], // Steel Rods
                'json' => json_encode(['description' => 'Steel Rods', 'specifications' => 'High-grade']),
                'quantity' => 100,
                'unit_price' => 25.50,
                'rental_start' => null,
                'rental_end' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $orderIds['ORD-2025-002'] ?? 2,
                'article_id' => $articleIds[1], // Electronic Components
                'json' => json_encode(['description' => 'Electronic Components', 'specifications' => '12V']),
                'quantity' => 50,
                'unit_price' => 15.75,
                'rental_start' => null,
                'rental_end' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $orderIds['RENT-2025-001'] ?? 3,
                'article_id' => $articleIds[2], // Consulting Services
                'json' => json_encode(['description' => 'Consulting Services', 'rate' => 'hourly']),
                'quantity' => 40,
                'unit_price' => 75.00,
                'rental_start' => now()->addDays(1),
                'rental_end' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($orderLines as $orderLine) {
            DB::table('order_lines')->insert($orderLine);
        }
    }
}
