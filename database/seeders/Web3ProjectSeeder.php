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
                'title' => 'atpad',
                'content' => '',
                'color' => 'yellow-500',
                'is_active' => true
            ],
            [
                'title' => 'swappy',
                'content' => '',
                'color' => 'gray-200',
                'is_active' => true
            ],
            [
                'title' => 'self',
                'content' => '',
                'color' => 'indigo-400',
                'is_active' => true
            ]
        ];

        foreach ($projects as $project) {
            DB::table('web3_projects')->insert($project);
        }
    }
} 