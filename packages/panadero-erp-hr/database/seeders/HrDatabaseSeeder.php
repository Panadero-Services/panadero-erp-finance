<?php

namespace Panadero\Erp\Hr\Database\Seeders;

use Illuminate\Database\Seeder;

class HrDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            HrDepartmentSeeder::class,
            HrEmployeeSeeder::class,
            HrVacancySeeder::class,
            HrApplicationSeeder::class,
            HrPerformanceReviewSeeder::class,
        ]);
    }
}
