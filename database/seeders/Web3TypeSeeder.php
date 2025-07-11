<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Web3TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'title' => 'token',
                'content' => '',
                'color' => 'gray',
                'is_active' => true
            ],
            [
                'title' => 'contract',
                'content' => '',
                'color' => 'gray',
                'is_active' => true
            ],
            [
                'title' => 'wallet',
                'content' => '',
                'color' => 'gray',
                'is_active' => true
            ],
            [
                'title' => 'chain',
                'content' => '',
                'color' => 'gray',
                'is_active' => true
            ]
        ];

        foreach ($types as $type) {
            DB::table('web3_types')->insert($type);
        }
    }
} 