<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplianceReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reports = [
            [
                'report_number' => 'RPT-2024-001',
                'title' => 'Q1 2024 Compliance Dashboard',
                'type' => 'internal',
                'category' => 'quarterly',
                'description' => 'Quarterly compliance status report covering all regulatory requirements',
                'status' => 'published',
                'report_date' => '2024-04-01',
                'period_start' => '2024-01-01',
                'period_end' => '2024-03-31',
                'data' => json_encode([
                    'total_policies' => 15,
                    'active_policies' => 12,
                    'expired_policies' => 3,
                    'audits_completed' => 3,
                    'audits_in_progress' => 1,
                    'risks_identified' => 8,
                    'risks_mitigated' => 5,
                    'compliance_score' => 87.5
                ]),
                'metrics' => json_encode([
                    'policy_compliance_rate' => 92.3,
                    'audit_completion_rate' => 85.7,
                    'risk_mitigation_rate' => 62.5,
                    'training_completion_rate' => 94.2
                ]),
                'file_path' => '/reports/compliance/q1-2024-dashboard.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'report_number' => 'RPT-2024-002',
                'title' => 'GDPR Compliance Report',
                'type' => 'regulatory',
                'category' => 'annual',
                'description' => 'Annual GDPR compliance assessment and data protection impact report',
                'status' => 'generated',
                'report_date' => '2024-05-15',
                'period_start' => '2023-05-01',
                'period_end' => '2024-04-30',
                'data' => json_encode([
                    'data_subjects' => 12500,
                    'data_breaches' => 0,
                    'consent_requests' => 245,
                    'data_retention_compliance' => 98.7,
                    'privacy_impact_assessments' => 12
                ]),
                'metrics' => json_encode([
                    'breach_notification_time' => 0,
                    'consent_withdrawal_rate' => 2.1,
                    'data_accuracy_rate' => 99.2,
                    'privacy_policy_acceptance' => 95.8
                ]),
                'file_path' => '/reports/gdpr/annual-2024.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'report_number' => 'RPT-2024-003',
                'title' => 'SOX Internal Controls Report',
                'type' => 'regulatory',
                'category' => 'annual',
                'description' => 'Management assessment of internal controls over financial reporting',
                'status' => 'draft',
                'report_date' => null,
                'period_start' => '2024-01-01',
                'period_end' => '2024-12-31',
                'data' => json_encode([
                    'control_deficiencies' => 3,
                    'material_weaknesses' => 0,
                    'significant_deficiencies' => 1,
                    'control_testing_completed' => 45,
                    'control_testing_pending' => 8
                ]),
                'metrics' => json_encode([
                    'control_effectiveness' => 94.2,
                    'testing_completion_rate' => 84.9,
                    'remediation_rate' => 78.6,
                    'management_override_incidents' => 0
                ]),
                'file_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'report_number' => 'RPT-2024-004',
                'title' => 'Risk Management Dashboard',
                'type' => 'internal',
                'category' => 'monthly',
                'description' => 'Monthly risk assessment and mitigation status report',
                'status' => 'published',
                'report_date' => '2024-04-30',
                'period_start' => '2024-04-01',
                'period_end' => '2024-04-30',
                'data' => json_encode([
                    'total_risks' => 12,
                    'high_risks' => 2,
                    'medium_risks' => 6,
                    'low_risks' => 4,
                    'mitigated_risks' => 3,
                    'new_risks' => 1
                ]),
                'metrics' => json_encode([
                    'risk_mitigation_rate' => 25.0,
                    'average_mitigation_time' => 45,
                    'risk_escalation_rate' => 8.3,
                    'risk_acceptance_rate' => 16.7
                ]),
                'file_path' => '/reports/risk/monthly-2024-04.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'report_number' => 'RPT-2024-005',
                'title' => 'Audit Findings Summary',
                'type' => 'audit',
                'category' => 'ad_hoc',
                'description' => 'Summary of audit findings and remediation status',
                'status' => 'approved',
                'report_date' => '2024-03-20',
                'period_start' => '2024-01-01',
                'period_end' => '2024-03-31',
                'data' => json_encode([
                    'total_findings' => 18,
                    'critical_findings' => 0,
                    'high_findings' => 3,
                    'medium_findings' => 8,
                    'low_findings' => 7,
                    'remediated_findings' => 12
                ]),
                'metrics' => json_encode([
                    'remediation_rate' => 66.7,
                    'average_remediation_time' => 28,
                    'repeat_findings' => 2,
                    'management_response_time' => 5
                ]),
                'file_path' => '/reports/audit/findings-q1-2024.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('compliance_reports')->insert($reports);
    }
}
