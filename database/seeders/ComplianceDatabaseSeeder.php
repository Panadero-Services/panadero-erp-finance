<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ComplianceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            CompliancePolicySeeder::class,
            ComplianceAuditSeeder::class,
            ComplianceRiskSeeder::class,
            ComplianceReportSeeder::class,
            ComplianceRCASeeder::class,
        ]);

        Model::reguard();
    }
}
