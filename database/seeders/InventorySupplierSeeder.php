<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_suppliers')->count() > 0) {
            $this->command->info('Inventory suppliers already exist, skipping...');
            return;
        }

        $suppliers = [
            [
                'name' => 'ABC Supplies Inc.',
                'contact_person' => 'Mike Johnson',
                'email' => 'mike@abcsupplies.com',
                'phone' => '+1-555-0123',
                'address' => '789 Supply Ave, Industrial District, City, State 12345',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'rating' => 4.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XYZ Components Ltd.',
                'contact_person' => 'Sarah Wilson',
                'email' => 'sarah@xyzcomponents.com',
                'phone' => '+1-555-0456',
                'address' => '321 Component St, Manufacturing Zone, City, State 12345',
                'payment_terms' => 'Net 15',
                'status' => 'active',
                'rating' => 4.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Materials Co.',
                'contact_person' => 'David Chen',
                'email' => 'david@globalmaterials.com',
                'phone' => '+1-555-0789',
                'address' => '456 Global Blvd, International District, City, State 12345',
                'payment_terms' => 'Net 45',
                'status' => 'active',
                'rating' => 4.8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Tools & More',
                'contact_person' => 'Lisa Anderson',
                'email' => 'lisa@techtools.com',
                'phone' => '+1-555-0321',
                'address' => '654 Tool Lane, Tech Park, City, State 12345',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'rating' => 3.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Raw Materials Direct',
                'contact_person' => 'Robert Taylor',
                'email' => 'robert@rawmaterials.com',
                'phone' => '+1-555-0654',
                'address' => '987 Material Way, Raw Goods District, City, State 12345',
                'payment_terms' => 'Net 20',
                'status' => 'active',
                'rating' => 4.1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Legacy Suppliers LLC',
                'contact_person' => 'Mary Davis',
                'email' => 'mary@legacysuppliers.com',
                'phone' => '+1-555-0987',
                'address' => '147 Legacy Rd, Old Industrial Area, City, State 12345',
                'payment_terms' => 'Net 60',
                'status' => 'inactive',
                'rating' => 2.5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('inventory_suppliers')->insert($suppliers);
    }
}
