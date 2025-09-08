<?php

namespace Panadero\Erp\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, and organizational development',
                'code' => 'HR',
                'budget' => 500000.00,
                'location' => 'Headquarters - Floor 3',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Engineering',
                'description' => 'Software development and technical innovation',
                'code' => 'ENG',
                'budget' => 2000000.00,
                'location' => 'Headquarters - Floor 2',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'description' => 'Brand management, digital marketing, and customer acquisition',
                'code' => 'MKT',
                'budget' => 800000.00,
                'location' => 'Headquarters - Floor 1',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales',
                'description' => 'Customer acquisition and revenue generation',
                'code' => 'SALES',
                'budget' => 1200000.00,
                'location' => 'Headquarters - Floor 1',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance',
                'description' => 'Financial planning, accounting, and budget management',
                'code' => 'FIN',
                'budget' => 300000.00,
                'location' => 'Headquarters - Floor 3',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operations',
                'description' => 'Business operations and process optimization',
                'code' => 'OPS',
                'budget' => 600000.00,
                'location' => 'Headquarters - Floor 2',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hr_departments')->insert($departments);
    }
}
