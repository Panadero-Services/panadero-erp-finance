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
                'id' => 1,
                'title' => 'token',
                'content' => '',
                'color' => 'gray',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 2,
                'title' => 'contract',
                'content' => '',
                'color' => 'gray',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 3,
                'title' => 'wallet',
                'content' => '',
                'color' => 'gray',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 4,
                'title' => 'chain',
                'content' => '',
                'color' => 'gray',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ]
        ];

        foreach ($types as $type) {
            DB::table('web3_types')->insert($type);
        }
    }
} 