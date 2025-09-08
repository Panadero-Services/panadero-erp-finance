<?php

namespace Panadero\ErpCompliance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompliancePolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $policies = [
            [
                'name' => 'GDPR Data Protection Policy',
                'type' => 'regulatory',
                'category' => 'gdpr',
                'description' => 'Comprehensive data protection policy in accordance with GDPR requirements',
                'content' => 'This policy outlines our commitment to protecting personal data and ensuring compliance with GDPR regulations...',
                'status' => 'active',
                'version' => '2.1',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'data_subject_rights' => true,
                    'consent_management' => true,
                    'data_breach_notification' => true,
                    'privacy_by_design' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SOX Financial Controls Policy',
                'type' => 'regulatory',
                'category' => 'sox',
                'description' => 'Sarbanes-Oxley compliance policy for financial reporting controls',
                'content' => 'This policy establishes internal controls over financial reporting to ensure accuracy and reliability...',
                'status' => 'active',
                'version' => '1.5',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'management_assessment' => true,
                    'auditor_attestation' => true,
                    'internal_controls' => true,
                    'disclosure_controls' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ISO 27001 Information Security Policy',
                'type' => 'industry',
                'category' => 'iso',
                'description' => 'Information security management system policy based on ISO 27001 standards',
                'content' => 'This policy provides the framework for managing information security risks and protecting information assets...',
                'status' => 'active',
                'version' => '3.0',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'risk_assessment' => true,
                    'security_controls' => true,
                    'incident_management' => true,
                    'continuous_improvement' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PCI DSS Payment Card Policy',
                'type' => 'regulatory',
                'category' => 'pci',
                'description' => 'Payment Card Industry Data Security Standard compliance policy',
                'content' => 'This policy ensures secure handling of payment card data and compliance with PCI DSS requirements...',
                'status' => 'active',
                'version' => '1.2',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'secure_network' => true,
                    'cardholder_data_protection' => true,
                    'vulnerability_management' => true,
                    'access_control' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HIPAA Privacy and Security Policy',
                'type' => 'regulatory',
                'category' => 'hipaa',
                'description' => 'Health Insurance Portability and Accountability Act compliance policy',
                'content' => 'This policy ensures protection of health information and compliance with HIPAA privacy and security rules...',
                'status' => 'active',
                'version' => '2.0',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'privacy_rule' => true,
                    'security_rule' => true,
                    'breach_notification' => true,
                    'business_associate_agreements' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internal Code of Conduct',
                'type' => 'internal',
                'category' => 'custom',
                'description' => 'Company-wide code of conduct and ethical standards',
                'content' => 'This policy establishes the ethical standards and behavioral expectations for all employees...',
                'status' => 'active',
                'version' => '4.0',
                'effective_date' => '2024-01-01',
                'expiry_date' => '2025-12-31',
                'requirements' => json_encode([
                    'ethical_behavior' => true,
                    'conflict_of_interest' => true,
                    'whistleblower_protection' => true,
                    'training_requirements' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('compliance_policies')->insert($policies);
    }
}
