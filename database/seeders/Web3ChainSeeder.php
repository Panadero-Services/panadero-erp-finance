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
                'title' => 'BSC',
                'chain_nr' => 56,
                'icon' => null,
                'is_active' => true
            ],
            [
                'title' => 'BSC Testnet',
                'chain_nr' => 97,
                'icon' => null,
                'is_active' => true
            ],
            [
                'title' => 'Goerli',
                'chain_nr' => 5,
                'icon' => null,
                'is_active' => true
            ],
            [
                'title' => 'Polygon',
                'chain_nr' => 137,
                'icon' => null,
                'is_active' => true
            ],
            [
                'title' => 'Polygon Testnet',
                'chain_nr' => 80001,
                'icon' => null,
                'is_active' => true
            ],
            [
                'title' => 'MultiVAC',
                'chain_nr' => 62621,
                'icon' => null,
                'is_active' => true
            ]
        ];

        foreach ($chains as $chain) {
            DB::table('web3_chains')->insert($chain);
        }
    }
} 