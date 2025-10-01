<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Seeding ERP customers...');

        $customers = [
            [
                'name' => 'Acme Corporation',
                'identifier' => 'ACME001',
                'comment' => 'Major industrial client',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['industry' => 'Manufacturing', 'priority' => 'high']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Solutions Ltd',
                'identifier' => 'TECH002',
                'comment' => 'Technology services company',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['industry' => 'Technology', 'priority' => 'medium']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Industries',
                'identifier' => 'GLOB003',
                'comment' => 'International trading company',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['industry' => 'Trading', 'priority' => 'high']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local Distributor',
                'identifier' => 'LOCAL04',
                'comment' => 'Regional distribution partner',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['industry' => 'Distribution', 'priority' => 'medium']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Startup Ventures',
                'identifier' => 'START05',
                'comment' => 'New startup client',
                'is_active' => true,
                'is_locked' => false,
                'json' => json_encode(['industry' => 'Startup', 'priority' => 'low']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->updateOrInsert(
                ['identifier' => $customer['identifier']],
                $customer
            );
        }

        $this->command->info('✅ ERP customers seeded successfully!');
    }
}
