<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Web3ProjectSeeder extends Seeder
{
    public function run()
    {
        $projects = [
            [
                'id' => 1,
                'title' => 'atpad',
                'content' => '',
                'color' => 'yellow-500',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 2,
                'title' => 'swappy',
                'content' => '',
                'color' => 'gray-200',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ],
            [
                'id' => 3,
                'title' => 'self',
                'content' => '',
                'color' => 'indigo-400',
                'created_at' => null,
                'updated_at' => null,
                'is_active' => true
            ]
        ];

        foreach ($projects as $project) {
            DB::table('web3_projects')->insert($project);
        }
    }
} 