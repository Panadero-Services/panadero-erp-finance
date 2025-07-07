<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use DB;

class I1OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('i1_business_customers')->pluck('id');
        $forwarders = DB::table('i1_forwarders')->pluck('id');
        $sites = DB::table('i1_sites')->pluck('id');
        $products = DB::table('i1_products')->pluck('id');
        $statuses = [1, 2, 3, 4, 5]; // Adjust to your actual status IDs

        if ($customers->isEmpty() || $forwarders->isEmpty() || $sites->isEmpty() || $products->isEmpty()) {
            $this->command->error('Seed dependencies missing: customers, forwarders, sites, or products.');
            return;
        }

        for ($i = 1; $i <= 50; $i++) {
            DB::table('i1_orders')->insert([
                'tpt_order_nr' => 'TPT-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'cust_order_nr' => 'CUST-' . strtoupper(Str::random(4)) . '-' . $i,
                'i1_customer_id' => $customers->random(),
                'i1_forwarder_id' => $forwarders->random(),
                'i1_source_site_id' => $sites->random(),
                'i1_dest_site_id' => $sites->random(),
                'i1_product_id' => $products->random(),
                'order_qty' => rand(10, 100),
                'i1_status_id' => $statuses[array_rand($statuses)],
                'r_date' => Carbon::now()->subDays(rand(0, 30)),
                'e_date' => Carbon::now()->addDays(rand(1, 30)),
                'docs_array' => '(CMR)',
                'analyse_array' => '',
                'comment' => 'Demo order ' . $i,
                'is_active' => rand(0, 1),
                'is_locked' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}