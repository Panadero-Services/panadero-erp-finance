<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceInvoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinanceInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user for seeding (or create one if none exists)
        $user = User::first() ?? User::factory()->create();

        // Sample invoice data for different sections
        $sampleInvoices = [
            // Accounts Payable (AP)
            [
                'invoice_number' => 'AP-INV-000001',
                'section' => 'AP',
                'type' => 'payable',
                'category' => 'expense',
                'description' => 'Office Supplies Invoice',
                'status' => 'approved',
                'amount' => 1250.00,
                'metadata' => [
                    'vendor_name' => 'Office Supplies Co.',
                    'vendor_id' => 'V001',
                    'invoice_date' => '2025-05-01',
                    'due_date' => '2025-05-31',
                    'items' => [
                        ['name' => 'Printer Paper', 'quantity' => 10, 'unit_price' => 25.00],
                        ['name' => 'Ink Cartridges', 'quantity' => 5, 'unit_price' => 200.00]
                    ]
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(5)
            ],
            [
                'invoice_number' => 'AP-INV-000002',
                'section' => 'AP',
                'type' => 'payable',
                'category' => 'service',
                'description' => 'IT Consulting Services',
                'status' => 'pending',
                'amount' => 5000.00,
                'metadata' => [
                    'vendor_name' => 'Tech Solutions Inc.',
                    'vendor_id' => 'V002',
                    'invoice_date' => '2025-05-05',
                    'due_date' => '2025-06-04',
                    'hours' => 40,
                    'rate' => 125.00
                ],
                'created_by' => $user->id
            ],

            // Accounts Receivable (AR)
            [
                'invoice_number' => 'AR-INV-000001',
                'section' => 'AR',
                'type' => 'receivable',
                'category' => 'revenue',
                'description' => 'Web Development Services',
                'status' => 'completed',
                'amount' => 15000.00,
                'metadata' => [
                    'customer_name' => 'ABC Company',
                    'customer_id' => 'C001',
                    'invoice_date' => '2025-05-01',
                    'due_date' => '2025-05-31',
                    'project' => 'E-commerce Website',
                    'milestone' => 'Phase 1 Complete'
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(10)
            ],
            [
                'invoice_number' => 'AR-INV-000002',
                'section' => 'AR',
                'type' => 'receivable',
                'category' => 'revenue',
                'description' => 'Mobile App Development',
                'status' => 'pending',
                'amount' => 25000.00,
                'metadata' => [
                    'customer_name' => 'XYZ Corporation',
                    'customer_id' => 'C002',
                    'invoice_date' => '2025-05-10',
                    'due_date' => '2025-06-09',
                    'project' => 'iOS/Android App',
                    'milestone' => 'Design Phase'
                ],
                'created_by' => $user->id
            ],

            // General Ledger (GL)
            [
                'invoice_number' => 'GL-JE-000001',
                'section' => 'GL',
                'type' => 'journal',
                'category' => 'adjustment',
                'description' => 'Monthly Depreciation Entry',
                'status' => 'approved',
                'amount' => 0.00,
                'metadata' => [
                    'period' => '2025-05',
                    'accounts' => [
                        ['account' => 'Depreciation Expense', 'debit' => 2500.00],
                        ['account' => 'Accumulated Depreciation', 'credit' => 2500.00]
                    ],
                    'entry_type' => 'monthly_close'
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(2)
            ],

            // Cash Flow (CF)
            [
                'invoice_number' => 'CF-TXN-000001',
                'section' => 'CF',
                'type' => 'cashflow',
                'category' => 'cash',
                'description' => 'Bank Transfer - Operating Account',
                'status' => 'completed',
                'amount' => 10000.00,
                'metadata' => [
                    'transaction_type' => 'inflow',
                    'bank_account' => 'Operating Account',
                    'reference' => 'TRX-001',
                    'category' => 'operating'
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(1)
            ],

            // Tax Records (TX)
            [
                'invoice_number' => 'TX-REC-000001',
                'section' => 'TX',
                'type' => 'tax',
                'category' => 'tax',
                'description' => 'Q1 2025 Income Tax Filing',
                'status' => 'pending',
                'amount' => 15000.00,
                'metadata' => [
                    'tax_type' => 'income_tax',
                    'tax_period' => 'Q1 2025',
                    'filing_due_date' => '2025-04-15',
                    'payment_due_date' => '2025-04-15',
                    'taxable_income' => 100000.00,
                    'tax_rate' => 0.15
                ],
                'created_by' => $user->id
            ],

            // Fixed Assets (FA)
            [
                'invoice_number' => 'FA-ASST-000001',
                'section' => 'FA',
                'type' => 'asset',
                'category' => 'asset',
                'description' => 'Computer Equipment Purchase',
                'status' => 'approved',
                'amount' => 8000.00,
                'metadata' => [
                    'asset_name' => 'Dell Workstation',
                    'asset_category' => 'Equipment',
                    'useful_life_months' => 36,
                    'depreciation_method' => 'straight_line',
                    'location' => 'IT Department',
                    'serial_number' => 'DELL-2025-001'
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(15)
            ],

            // Budget (BD)
            [
                'invoice_number' => 'BD-BGT-000001',
                'section' => 'BD',
                'type' => 'budget',
                'category' => 'budget',
                'description' => 'Q2 2025 Operating Budget',
                'status' => 'approved',
                'amount' => 0.00,
                'metadata' => [
                    'budget_period' => 'Q2 2025',
                    'budget_type' => 'operating',
                    'departments' => [
                        ['name' => 'Sales', 'amount' => 50000.00],
                        ['name' => 'Marketing', 'amount' => 25000.00],
                        ['name' => 'Operations', 'amount' => 35000.00]
                    ],
                    'total_budget' => 110000.00
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(20)
            ],

            // Audit Logs (AU)
            [
                'invoice_number' => 'AU-LOG-000001',
                'section' => 'AU',
                'type' => 'audit',
                'category' => 'audit',
                'description' => 'User Permission Change Audit',
                'status' => 'completed',
                'amount' => 0.00,
                'metadata' => [
                    'audit_type' => 'user_permission_change',
                    'user_id' => $user->id,
                    'action' => 'role_assigned',
                    'old_roles' => ['user'],
                    'new_roles' => ['user', 'manager'],
                    'changed_by' => $user->id,
                    'ip_address' => '192.168.1.100'
                ],
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(3)
            ]
        ];

        // Insert sample invoices
        foreach ($sampleInvoices as $invoiceData) {
            FinanceInvoice::create($invoiceData);
        }

        $this->command->info('Finance Invoice seeder completed successfully!');
        $this->command->info('Created ' . count($sampleInvoices) . ' sample invoices across all sections.');
    }
}

