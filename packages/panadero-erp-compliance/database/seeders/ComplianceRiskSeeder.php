<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplianceRiskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $risks = [
            [
                'risk_id' => 'RISK-2024-001',
                'title' => 'Data Breach Risk',
                'description' => 'Risk of unauthorized access to personal data resulting in GDPR violations',
                'category' => 'regulatory',
                'type' => 'data',
                'status' => 'identified',
                'probability' => 'medium',
                'impact' => 'high',
                'risk_level' => 'high',
                'mitigation_plan' => 'Implement multi-factor authentication, encryption at rest and in transit, regular security training',
                'controls' => 'Access controls, encryption, monitoring, incident response plan',
                'identified_date' => '2024-01-15',
                'target_resolution_date' => '2024-06-30',
                'actual_resolution_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'risk_id' => 'RISK-2024-002',
                'title' => 'Financial Reporting Error',
                'description' => 'Risk of material misstatement in financial reports due to control weaknesses',
                'category' => 'regulatory',
                'type' => 'compliance',
                'status' => 'assessed',
                'probability' => 'low',
                'impact' => 'high',
                'risk_level' => 'medium',
                'mitigation_plan' => 'Strengthen segregation of duties, implement automated controls, enhance review processes',
                'controls' => 'Segregation of duties, automated reconciliations, management review, external audit',
                'identified_date' => '2024-02-01',
                'target_resolution_date' => '2024-08-31',
                'actual_resolution_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'risk_id' => 'RISK-2024-003',
                'title' => 'Third-Party Vendor Risk',
                'description' => 'Risk of compliance violations through third-party vendors and suppliers',
                'category' => 'operational',
                'type' => 'compliance',
                'status' => 'mitigated',
                'probability' => 'medium',
                'impact' => 'medium',
                'risk_level' => 'medium',
                'mitigation_plan' => 'Implement vendor due diligence, contract requirements, regular assessments',
                'controls' => 'Vendor assessments, contract clauses, monitoring, termination procedures',
                'identified_date' => '2024-01-01',
                'target_resolution_date' => '2024-03-31',
                'actual_resolution_date' => '2024-03-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'risk_id' => 'RISK-2024-004',
                'title' => 'Regulatory Change Risk',
                'description' => 'Risk of non-compliance due to changes in regulatory requirements',
                'category' => 'regulatory',
                'type' => 'compliance',
                'status' => 'identified',
                'probability' => 'high',
                'impact' => 'medium',
                'risk_level' => 'high',
                'mitigation_plan' => 'Establish regulatory monitoring, impact assessment process, change management',
                'controls' => 'Regulatory monitoring, impact assessments, policy updates, training',
                'identified_date' => '2024-01-01',
                'target_resolution_date' => '2024-12-31',
                'actual_resolution_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'risk_id' => 'RISK-2024-005',
                'title' => 'Employee Misconduct Risk',
                'description' => 'Risk of compliance violations due to employee misconduct or lack of awareness',
                'category' => 'reputational',
                'type' => 'compliance',
                'status' => 'assessed',
                'probability' => 'low',
                'impact' => 'high',
                'risk_level' => 'medium',
                'mitigation_plan' => 'Enhance training programs, implement monitoring, establish reporting mechanisms',
                'controls' => 'Training programs, code of conduct, monitoring, whistleblower protection',
                'identified_date' => '2024-02-15',
                'target_resolution_date' => '2024-09-30',
                'actual_resolution_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'risk_id' => 'RISK-2024-006',
                'title' => 'System Security Vulnerability',
                'description' => 'Risk of security vulnerabilities in IT systems leading to compliance breaches',
                'category' => 'operational',
                'type' => 'security',
                'status' => 'mitigated',
                'probability' => 'medium',
                'impact' => 'high',
                'risk_level' => 'high',
                'mitigation_plan' => 'Implement vulnerability management, regular patching, security testing',
                'controls' => 'Vulnerability scanning, patch management, penetration testing, monitoring',
                'identified_date' => '2024-01-10',
                'target_resolution_date' => '2024-04-30',
                'actual_resolution_date' => '2024-04-15',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('compliance_risks')->insert($risks);
    }
}
