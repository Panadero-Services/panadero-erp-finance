<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplianceAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $audits = [
            [
                'audit_number' => 'AUD-2024-001',
                'title' => 'Q1 2024 GDPR Compliance Audit',
                'type' => 'regulatory',
                'scope' => 'full',
                'description' => 'Comprehensive audit of GDPR compliance across all data processing activities',
                'status' => 'completed',
                'start_date' => '2024-01-15',
                'end_date' => '2024-03-15',
                'due_date' => '2024-03-31',
                'findings' => json_encode([
                    'critical' => 0,
                    'high' => 2,
                    'medium' => 5,
                    'low' => 8
                ]),
                'recommendations' => json_encode([
                    'Implement automated data retention policies',
                    'Enhance consent management system',
                    'Conduct privacy impact assessments for new projects'
                ]),
                'risk_level' => 'medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'audit_number' => 'AUD-2024-002',
                'title' => 'SOX Financial Controls Review',
                'type' => 'regulatory',
                'scope' => 'full',
                'description' => 'Annual review of internal controls over financial reporting',
                'status' => 'in_progress',
                'start_date' => '2024-04-01',
                'end_date' => null,
                'due_date' => '2024-06-30',
                'findings' => json_encode([
                    'critical' => 0,
                    'high' => 1,
                    'medium' => 3,
                    'low' => 4
                ]),
                'recommendations' => json_encode([
                    'Strengthen segregation of duties',
                    'Implement automated reconciliation processes',
                    'Enhance documentation of control activities'
                ]),
                'risk_level' => 'high',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'audit_number' => 'AUD-2024-003',
                'title' => 'ISO 27001 Security Audit',
                'type' => 'external',
                'scope' => 'full',
                'description' => 'Third-party audit of information security management system',
                'status' => 'planned',
                'start_date' => '2024-07-01',
                'end_date' => null,
                'due_date' => '2024-09-30',
                'findings' => null,
                'recommendations' => null,
                'risk_level' => 'medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'audit_number' => 'AUD-2024-004',
                'title' => 'PCI DSS Compliance Assessment',
                'type' => 'regulatory',
                'scope' => 'partial',
                'description' => 'Assessment of payment card data security controls',
                'status' => 'completed',
                'start_date' => '2024-02-01',
                'end_date' => '2024-02-28',
                'due_date' => '2024-03-15',
                'findings' => json_encode([
                    'critical' => 0,
                    'high' => 0,
                    'medium' => 2,
                    'low' => 3
                ]),
                'recommendations' => json_encode([
                    'Update encryption standards',
                    'Implement network segmentation',
                    'Enhance access logging'
                ]),
                'risk_level' => 'low',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'audit_number' => 'AUD-2024-005',
                'title' => 'Internal Control Self-Assessment',
                'type' => 'internal',
                'scope' => 'full',
                'description' => 'Quarterly self-assessment of internal controls',
                'status' => 'completed',
                'start_date' => '2024-03-01',
                'end_date' => '2024-03-31',
                'due_date' => '2024-04-15',
                'findings' => json_encode([
                    'critical' => 0,
                    'high' => 0,
                    'medium' => 1,
                    'low' => 2
                ]),
                'recommendations' => json_encode([
                    'Improve control documentation',
                    'Enhance monitoring procedures'
                ]),
                'risk_level' => 'low',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('compliance_audits')->insert($audits);
    }
}
