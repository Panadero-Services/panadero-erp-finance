<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Web3ChainSeeder extends Seeder
{
    public function run()
    {
        $chains = [
            [
                'id' => 1,
                'title' => 'BSC',
                'chain_nr' => 56,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 2,
                'title' => 'BSC Testnet',
                'chain_nr' => 97,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 3,
                'title' => 'Goerli',
                'chain_nr' => 5,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 4,
                'title' => 'Polygon',
                'chain_nr' => 137,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 5,
                'title' => 'Polygon Testnet',
                'chain_nr' => 80001,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 6,
                'title' => 'MultiVAC',
                'chain_nr' => 62621,
                'icon' => null,
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ]
        ];

        foreach ($chains as $chain) {
            DB::table('web3_chains')->insert($chain);
        }
    }
} 