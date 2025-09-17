<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryPartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('inventory_partners')->count() > 0) {
            $this->command->info('Inventory partners already exist, skipping...');
            return;
        }

        $partners = [
            // Suppliers
            [
                'name' => 'Organic Valley Foods',
                'type' => 'supplier',
                'contact_person' => 'John Anderson',
                'email' => 'john@organicvalley.com',
                'phone' => '+1-555-0101',
                'address' => json_encode([
                    'street' => '123 Organic Way',
                    'city' => 'Green Valley',
                    'state' => 'CA',
                    'zip' => '90210',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-OV-001',
                'payment_terms' => 'Net 30',
                'credit_limit' => 50000.00,
                'rating' => 4.8,
                'is_active' => true,
                'notes' => 'Premium organic supplier with excellent quality',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'KitchenPro Equipment',
                'type' => 'supplier',
                'contact_person' => 'Sarah Mitchell',
                'email' => 'sarah@kitchenpro.com',
                'phone' => '+1-555-0102',
                'address' => json_encode([
                    'street' => '456 Industrial Blvd',
                    'city' => 'Manufacturing City',
                    'state' => 'TX',
                    'zip' => '75001',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-KP-002',
                'payment_terms' => 'Net 15',
                'credit_limit' => 75000.00,
                'rating' => 4.6,
                'is_active' => true,
                'notes' => 'Professional kitchen equipment supplier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mediterranean Gold Imports',
                'type' => 'supplier',
                'contact_person' => 'Marco Rossi',
                'email' => 'marco@medgold.com',
                'phone' => '+1-555-0103',
                'address' => json_encode([
                    'street' => '789 Import Street',
                    'city' => 'Port City',
                    'state' => 'FL',
                    'zip' => '33101',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-MG-003',
                'payment_terms' => 'Net 45',
                'credit_limit' => 30000.00,
                'rating' => 4.4,
                'is_active' => true,
                'notes' => 'Specialized in Mediterranean products',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Customers
            [
                'name' => 'Smith Bakery',
                'type' => 'customer',
                'contact_person' => 'John Smith',
                'email' => 'john@smithbakery.com',
                'phone' => '+1-555-0201',
                'address' => json_encode([
                    'street' => '123 Main Street',
                    'city' => 'New York',
                    'state' => 'NY',
                    'zip' => '10001',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-SB-001',
                'payment_terms' => 'Net 30',
                'credit_limit' => 25000.00,
                'rating' => 4.9,
                'is_active' => true,
                'notes' => 'Regular customer with high volume orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Garcia Restaurant',
                'type' => 'customer',
                'contact_person' => 'Maria Garcia',
                'email' => 'maria@garciarestaurant.com',
                'phone' => '+1-555-0202',
                'address' => json_encode([
                    'street' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'zip' => '90210',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-GR-002',
                'payment_terms' => 'Net 15',
                'credit_limit' => 15000.00,
                'rating' => 4.7,
                'is_active' => true,
                'notes' => 'Upscale restaurant with quality requirements',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Johnson Cafe',
                'type' => 'customer',
                'contact_person' => 'David Johnson',
                'email' => 'david@johnsoncafe.com',
                'phone' => '+1-555-0203',
                'address' => json_encode([
                    'street' => '789 Pine Street',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'zip' => '60601',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-JC-003',
                'payment_terms' => 'Net 30',
                'credit_limit' => 10000.00,
                'rating' => 4.5,
                'is_active' => true,
                'notes' => 'Local cafe with consistent orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wilson Catering',
                'type' => 'customer',
                'contact_person' => 'Sarah Wilson',
                'email' => 'sarah@wilsoncatering.com',
                'phone' => '+1-555-0204',
                'address' => json_encode([
                    'street' => '321 Elm Street',
                    'city' => 'Miami',
                    'state' => 'FL',
                    'zip' => '33101',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-WC-004',
                'payment_terms' => 'Net 30',
                'credit_limit' => 20000.00,
                'rating' => 4.8,
                'is_active' => true,
                'notes' => 'Event catering with seasonal demand',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brown Bakery',
                'type' => 'customer',
                'contact_person' => 'Michael Brown',
                'email' => 'michael@brownbakery.com',
                'phone' => '+1-555-0205',
                'address' => json_encode([
                    'street' => '654 Maple Drive',
                    'city' => 'Seattle',
                    'state' => 'WA',
                    'zip' => '98101',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-BB-005',
                'payment_terms' => 'Net 30',
                'credit_limit' => 5000.00,
                'rating' => 4.2,
                'is_active' => true,
                'notes' => 'New customer - first order placed',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Forwarders
            [
                'name' => 'FastTrack Logistics',
                'type' => 'forwarder',
                'contact_person' => 'Lisa Chen',
                'email' => 'lisa@fasttrack.com',
                'phone' => '+1-555-0301',
                'address' => json_encode([
                    'street' => '100 Logistics Way',
                    'city' => 'Transport City',
                    'state' => 'NJ',
                    'zip' => '07001',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-FT-001',
                'payment_terms' => 'Net 30',
                'credit_limit' => 0.00,
                'rating' => 4.6,
                'is_active' => true,
                'notes' => 'Reliable shipping partner for domestic deliveries',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Shipping Solutions',
                'type' => 'forwarder',
                'contact_person' => 'Robert Kim',
                'email' => 'robert@globalshipping.com',
                'phone' => '+1-555-0302',
                'address' => json_encode([
                    'street' => '200 International Blvd',
                    'city' => 'Port City',
                    'state' => 'CA',
                    'zip' => '90731',
                    'country' => 'USA'
                ]),
                'tax_id' => 'TAX-GS-002',
                'payment_terms' => 'Net 30',
                'credit_limit' => 0.00,
                'rating' => 4.4,
                'is_active' => true,
                'notes' => 'International shipping and customs handling',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventory_partners')->insert($partners);
    }
}
