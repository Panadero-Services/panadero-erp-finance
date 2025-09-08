<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplianceRCASeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rcaCases = [
            [
                'rca_id' => 'RCA-2024-001',
                'title' => 'Data Processing Consent Violation',
                'description' => 'Investigation into unauthorized data processing without proper consent',
                'incident_type' => 'compliance_violation',
                'severity' => 'high',
                'status' => 'completed',
                'problem_statement' => 'Customer data was processed for marketing purposes without explicit consent, violating GDPR Article 6 requirements.',
                'immediate_actions' => 'Immediately halt all unauthorized data processing, notify data subjects, and implement consent verification system.',
                'root_causes' => json_encode([
                    'Insufficient consent management system',
                    'Lack of data processing documentation',
                    'Inadequate staff training on GDPR requirements',
                    'Missing automated consent verification'
                ]),
                'contributing_factors' => json_encode([
                    'Legacy system integration issues',
                    'Rapid business growth outpacing compliance processes',
                    'Insufficient oversight of third-party data processors',
                    'Lack of regular compliance audits'
                ]),
                'corrective_actions' => json_encode([
                    'Implement automated consent management system',
                    'Update data processing documentation',
                    'Conduct comprehensive GDPR training',
                    'Establish regular compliance monitoring'
                ]),
                'preventive_measures' => json_encode([
                    'Regular compliance training programs',
                    'Automated consent verification workflows',
                    'Quarterly compliance assessments',
                    'Enhanced data processing oversight'
                ]),
                'incident_date' => '2024-02-15',
                'discovery_date' => '2024-02-20',
                'target_resolution_date' => '2024-04-15',
                'actual_resolution_date' => '2024-04-10',
                'ai_analysis' => json_encode([
                    'risk_score' => 8.5,
                    'similar_incidents' => 3,
                    'recommended_actions' => [
                        'Implement AI-powered consent monitoring',
                        'Deploy automated compliance checking',
                        'Enhance data lineage tracking'
                    ],
                    'predicted_likelihood' => 'low',
                    'confidence_score' => 0.87
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rca_id' => 'RCA-2024-002',
                'title' => 'Financial Control Weakness',
                'description' => 'Root cause analysis of identified control deficiency in accounts payable process',
                'incident_type' => 'audit_finding',
                'severity' => 'medium',
                'status' => 'in_progress',
                'problem_statement' => 'Lack of segregation of duties in the accounts payable approval process, creating risk of unauthorized payments.',
                'immediate_actions' => 'Implement temporary dual approval for all payments over $10,000, conduct immediate review of recent transactions.',
                'root_causes' => json_encode([
                    'Insufficient segregation of duties',
                    'Lack of automated approval workflows',
                    'Inadequate monitoring of payment approvals',
                    'Missing exception reporting'
                ]),
                'contributing_factors' => json_encode([
                    'Staff shortage in accounting department',
                    'Legacy system limitations',
                    'Insufficient training on control requirements',
                    'Lack of regular control testing'
                ]),
                'corrective_actions' => json_encode([
                    'Implement automated approval workflows',
                    'Establish segregation of duties matrix',
                    'Deploy exception reporting system',
                    'Conduct control effectiveness testing'
                ]),
                'preventive_measures' => json_encode([
                    'Regular control testing and monitoring',
                    'Automated workflow controls',
                    'Enhanced staff training programs',
                    'Quarterly control assessments'
                ]),
                'incident_date' => '2024-03-01',
                'discovery_date' => '2024-03-05',
                'target_resolution_date' => '2024-06-30',
                'actual_resolution_date' => null,
                'ai_analysis' => json_encode([
                    'risk_score' => 6.2,
                    'similar_incidents' => 1,
                    'recommended_actions' => [
                        'Deploy AI-powered anomaly detection',
                        'Implement predictive control monitoring',
                        'Enhance automated workflow controls'
                    ],
                    'predicted_likelihood' => 'medium',
                    'confidence_score' => 0.73
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rca_id' => 'RCA-2024-003',
                'title' => 'Security Incident Response',
                'description' => 'Analysis of security incident involving potential data exposure',
                'incident_type' => 'risk_event',
                'severity' => 'critical',
                'status' => 'completed',
                'problem_statement' => 'Unauthorized access attempt to customer database through compromised employee credentials.',
                'immediate_actions' => 'Immediately revoke compromised credentials, isolate affected systems, notify security team, and assess data exposure.',
                'root_causes' => json_encode([
                    'Weak password policy enforcement',
                    'Lack of multi-factor authentication',
                    'Insufficient access monitoring',
                    'Delayed security incident response'
                ]),
                'contributing_factors' => json_encode([
                    'Legacy authentication system',
                    'Insufficient security awareness training',
                    'Lack of automated threat detection',
                    'Inadequate incident response procedures'
                ]),
                'corrective_actions' => json_encode([
                    'Implement mandatory MFA for all accounts',
                    'Deploy advanced threat detection',
                    'Enhance security monitoring',
                    'Update incident response procedures'
                ]),
                'preventive_measures' => json_encode([
                    'Regular security training and testing',
                    'Automated security monitoring',
                    'Regular penetration testing',
                    'Enhanced access control policies'
                ]),
                'incident_date' => '2024-01-20',
                'discovery_date' => '2024-01-20',
                'target_resolution_date' => '2024-03-31',
                'actual_resolution_date' => '2024-03-15',
                'ai_analysis' => json_encode([
                    'risk_score' => 9.1,
                    'similar_incidents' => 0,
                    'recommended_actions' => [
                        'Deploy AI-powered threat detection',
                        'Implement behavioral analytics',
                        'Enhance automated response systems'
                    ],
                    'predicted_likelihood' => 'very_low',
                    'confidence_score' => 0.92
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rca_id' => 'RCA-2024-004',
                'title' => 'Policy Violation Investigation',
                'description' => 'Investigation into employee violation of company code of conduct',
                'incident_type' => 'policy_breach',
                'severity' => 'medium',
                'status' => 'open',
                'problem_statement' => 'Employee engaged in activities that violated company conflict of interest policy.',
                'immediate_actions' => 'Suspend employee pending investigation, secure relevant documentation, notify legal and HR departments.',
                'root_causes' => json_encode([
                    'Insufficient policy awareness training',
                    'Lack of clear reporting mechanisms',
                    'Inadequate oversight of employee activities',
                    'Missing conflict of interest monitoring'
                ]),
                'contributing_factors' => json_encode([
                    'Rapid organizational changes',
                    'Insufficient management oversight',
                    'Lack of regular policy training',
                    'Inadequate monitoring systems'
                ]),
                'corrective_actions' => json_encode([
                    'Enhance policy training programs',
                    'Implement conflict of interest monitoring',
                    'Establish clear reporting procedures',
                    'Conduct regular policy compliance audits'
                ]),
                'preventive_measures' => json_encode([
                    'Regular ethics and compliance training',
                    'Automated conflict monitoring',
                    'Enhanced management oversight',
                    'Quarterly policy compliance reviews'
                ]),
                'incident_date' => '2024-04-10',
                'discovery_date' => '2024-04-12',
                'target_resolution_date' => '2024-07-31',
                'actual_resolution_date' => null,
                'ai_analysis' => json_encode([
                    'risk_score' => 5.8,
                    'similar_incidents' => 2,
                    'recommended_actions' => [
                        'Implement AI-powered compliance monitoring',
                        'Deploy automated policy violation detection',
                        'Enhance behavioral analytics'
                    ],
                    'predicted_likelihood' => 'low',
                    'confidence_score' => 0.68
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('compliance_rca')->insert($rcaCases);
    }
}
