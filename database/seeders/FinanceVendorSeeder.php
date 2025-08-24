<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\User;
use Carbon\Carbon;

class FinanceVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating finance vendor records...');

        // Get users for audit fields
        $users = User::all();
        $userId = $users->isNotEmpty() ? $users->first()->id : 1;

        // Get vendors to create finance records for
        $vendors = Vendor::where('is_approved', true)->get();

        if ($vendors->isEmpty()) {
            $this->command->error('No approved vendors found. Please run VendorSeeder first.');
            return;
        }

        $financeVendors = [];

        foreach ($vendors as $vendor) {
            switch ($vendor->vendor_code) {
                case 'ACME001':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 30',
                        'payment_terms_days' => 30,
                        'credit_limit' => 100000.00,
                        'current_balance' => 15750.50,
                        'preferred_payment_method' => 'wire',
                        'bank_name' => 'First National Bank',
                        'bank_account_number' => '****1234',
                        'bank_routing_number' => '021000021',
                        'iban' => null,
                        'swift_code' => 'FNBKUS33',
                        'default_expense_account_code' => '5000',
                        'is_1099_vendor' => false,
                        'ytd_payments' => 85600.75,
                        'tax_classification' => 'Corporation',
                        'preferred_categories' => json_encode(['Raw Materials', 'Manufacturing Supplies']),
                        'minimum_order_amount' => 1000.00,
                        'requires_po' => true,
                        'lead_time_days' => 14,
                        'average_rating' => 4.8,
                        'total_orders' => 24,
                        'total_spent' => 245600.25,
                        'last_order_date' => Carbon::now()->subDays(5),
                        'last_payment_date' => Carbon::now()->subDays(8),
                        'auto_approve_invoices' => true,
                        'auto_approve_limit' => 5000.00,
                        'default_gl_account' => '5000-001',
                        'cost_center' => 'PROD-001',
                        'department' => 'Manufacturing',
                        'required_documents' => json_encode(['Certificate of Insurance', 'W-9 Form']),
                        'contract_start_date' => Carbon::now()->subYear(),
                        'contract_end_date' => Carbon::now()->addYear(),
                        'background_check_required' => false,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(6),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'USD',
                            'discount_terms' => '2/10 Net 30',
                            'volume_discounts' => true
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(30),
                        'updated_at' => Carbon::now()->subDays(5),
                    ];
                    break;

                case 'TECH002':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 15',
                        'payment_terms_days' => 15,
                        'credit_limit' => 50000.00,
                        'current_balance' => 8500.00,
                        'preferred_payment_method' => 'ach',
                        'bank_name' => 'Silicon Valley Bank',
                        'bank_account_number' => '****5678',
                        'bank_routing_number' => '121140399',
                        'iban' => null,
                        'swift_code' => 'SVBKUS6S',
                        'default_expense_account_code' => '5100',
                        'is_1099_vendor' => true,
                        'ytd_payments' => 42300.00,
                        'tax_classification' => 'Service Provider',
                        'preferred_categories' => json_encode(['IT Services', 'Software Licenses', 'Consulting']),
                        'minimum_order_amount' => 500.00,
                        'requires_po' => false,
                        'lead_time_days' => 5,
                        'average_rating' => 4.9,
                        'total_orders' => 18,
                        'total_spent' => 127800.50,
                        'last_order_date' => Carbon::now()->subDays(2),
                        'last_payment_date' => Carbon::now()->subDays(3),
                        'auto_approve_invoices' => true,
                        'auto_approve_limit' => 2500.00,
                        'default_gl_account' => '5100-002',
                        'cost_center' => 'IT-001',
                        'department' => 'Information Technology',
                        'required_documents' => json_encode(['W-9 Form', 'Service Agreement']),
                        'contract_start_date' => Carbon::now()->subMonths(8),
                        'contract_end_date' => Carbon::now()->addMonths(16),
                        'background_check_required' => true,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(9),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'USD',
                            'retainer_agreement' => true,
                            'hourly_rate_cap' => 150.00
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(25),
                        'updated_at' => Carbon::now()->subDays(3),
                    ];
                    break;

                case 'CLEAN003':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 15',
                        'payment_terms_days' => 15,
                        'credit_limit' => 15000.00,
                        'current_balance' => 2800.00,
                        'preferred_payment_method' => 'check',
                        'bank_name' => 'Community First Bank',
                        'bank_account_number' => '****9012',
                        'bank_routing_number' => '071000013',
                        'iban' => null,
                        'swift_code' => null,
                        'default_expense_account_code' => '5200',
                        'is_1099_vendor' => true,
                        'ytd_payments' => 18600.00,
                        'tax_classification' => 'Service Provider',
                        'preferred_categories' => json_encode(['Facility Services', 'Maintenance', 'Supplies']),
                        'minimum_order_amount' => 200.00,
                        'requires_po' => false,
                        'lead_time_days' => 1,
                        'average_rating' => 4.2,
                        'total_orders' => 12,
                        'total_spent' => 36800.75,
                        'last_order_date' => Carbon::now()->subDays(7),
                        'last_payment_date' => Carbon::now()->subDays(10),
                        'auto_approve_invoices' => false,
                        'auto_approve_limit' => null,
                        'default_gl_account' => '5200-001',
                        'cost_center' => 'FAC-001',
                        'department' => 'Facilities',
                        'required_documents' => json_encode(['Certificate of Insurance', 'W-9 Form', 'Bond Certificate']),
                        'contract_start_date' => Carbon::now()->subMonths(6),
                        'contract_end_date' => Carbon::now()->addMonths(18),
                        'background_check_required' => true,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(3),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'USD',
                            'recurring_service' => true,
                            'monthly_contract_amount' => 2400.00
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(20),
                        'updated_at' => Carbon::now()->subDays(2),
                    ];
                    break;

                case 'CONST004':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 45',
                        'payment_terms_days' => 45,
                        'credit_limit' => 250000.00,
                        'current_balance' => 45600.00,
                        'preferred_payment_method' => 'wire',
                        'bank_name' => 'Texas Regional Bank',
                        'bank_account_number' => '****3456',
                        'bank_routing_number' => '111000025',
                        'iban' => null,
                        'swift_code' => 'TRBKUS44',
                        'default_expense_account_code' => '5300',
                        'is_1099_vendor' => true,
                        'ytd_payments' => 189500.00,
                        'tax_classification' => 'Contractor',
                        'preferred_categories' => json_encode(['Construction', 'Building Materials', 'Labor']),
                        'minimum_order_amount' => 5000.00,
                        'requires_po' => true,
                        'lead_time_days' => 30,
                        'average_rating' => 4.5,
                        'total_orders' => 6,
                        'total_spent' => 425900.00,
                        'last_order_date' => Carbon::now()->subDays(12),
                        'last_payment_date' => Carbon::now()->subDays(15),
                        'auto_approve_invoices' => false,
                        'auto_approve_limit' => null,
                        'default_gl_account' => '5300-001',
                        'cost_center' => 'CONST-001',
                        'department' => 'Construction',
                        'required_documents' => json_encode([
                            'License Certificate', 
                            'Certificate of Insurance', 
                            'W-9 Form', 
                            'Safety Certification'
                        ]),
                        'contract_start_date' => Carbon::now()->subMonths(4),
                        'contract_end_date' => Carbon::now()->addMonths(14),
                        'background_check_required' => true,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(8),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'USD',
                            'progress_billing' => true,
                            'retention_percentage' => 10.0
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(15),
                        'updated_at' => Carbon::now()->subDays(1),
                    ];
                    break;

                case 'CONSULT005':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 30',
                        'payment_terms_days' => 30,
                        'credit_limit' => 75000.00,
                        'current_balance' => 12500.00,
                        'preferred_payment_method' => 'ach',
                        'bank_name' => 'Bank of Boston',
                        'bank_account_number' => '****7890',
                        'bank_routing_number' => '011000138',
                        'iban' => null,
                        'swift_code' => 'BKBOUS33',
                        'default_expense_account_code' => '5400',
                        'is_1099_vendor' => true,
                        'ytd_payments' => 68750.00,
                        'tax_classification' => 'Professional Services',
                        'preferred_categories' => json_encode(['Consulting', 'Advisory Services', 'Training']),
                        'minimum_order_amount' => 2500.00,
                        'requires_po' => true,
                        'lead_time_days' => 7,
                        'average_rating' => 5.0,
                        'total_orders' => 8,
                        'total_spent' => 156250.00,
                        'last_order_date' => Carbon::now()->subDays(1),
                        'last_payment_date' => Carbon::now()->subDays(5),
                        'auto_approve_invoices' => false,
                        'auto_approve_limit' => null,
                        'default_gl_account' => '5400-001',
                        'cost_center' => 'EXEC-001',
                        'department' => 'Executive',
                        'required_documents' => json_encode([
                            'Professional License', 
                            'W-9 Form', 
                            'NDA Agreement',
                            'Consulting Agreement'
                        ]),
                        'contract_start_date' => Carbon::now()->subMonths(3),
                        'contract_end_date' => Carbon::now()->addYear(),
                        'background_check_required' => true,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(15),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'USD',
                            'project_based_billing' => true,
                            'hourly_rate' => 250.00
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(10),
                        'updated_at' => Carbon::now(),
                    ];
                    break;

                case 'GLOBAL007':
                    $financeVendors[] = [
                        'vendor_id' => $vendor->id,
                        'payment_terms' => 'Net 30',
                        'payment_terms_days' => 30,
                        'credit_limit' => 80000.00,
                        'current_balance' => 18750.00,
                        'preferred_payment_method' => 'wire',
                        'bank_name' => 'ING Bank N.V.',
                        'bank_account_number' => 'NL****4567',
                        'bank_routing_number' => null,
                        'iban' => 'NL91INGB0002445588',
                        'swift_code' => 'INGBNL2A',
                        'default_expense_account_code' => '5500',
                        'is_1099_vendor' => false,
                        'ytd_payments' => 52800.00,
                        'tax_classification' => 'Foreign Corporation',
                        'preferred_categories' => json_encode(['Logistics', 'Freight', 'International Services']),
                        'minimum_order_amount' => 1500.00,
                        'requires_po' => true,
                        'lead_time_days' => 21,
                        'average_rating' => 4.6,
                        'total_orders' => 16,
                        'total_spent' => 198400.50,
                        'last_order_date' => Carbon::now()->subDays(4),
                        'last_payment_date' => Carbon::now()->subDays(6),
                        'auto_approve_invoices' => true,
                        'auto_approve_limit' => 3000.00,
                        'default_gl_account' => '5500-001',
                        'cost_center' => 'LOG-001',
                        'department' => 'Logistics',
                        'required_documents' => json_encode([
                            'Tax Exemption Certificate', 
                            'VAT Registration',
                            'Freight License'
                        ]),
                        'contract_start_date' => Carbon::now()->subMonths(12),
                        'contract_end_date' => Carbon::now()->addYear(),
                        'background_check_required' => false,
                        'insurance_required' => true,
                        'insurance_expiry_date' => Carbon::now()->addMonths(10),
                        'finance_metadata' => json_encode([
                            'preferred_currency' => 'EUR',
                            'currency_hedge' => true,
                            'incoterms' => 'CIF'
                        ]),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now()->subDays(45),
                        'updated_at' => Carbon::now()->subDays(7),
                    ];
                    break;
            }
        }

        foreach ($financeVendors as $financeVendor) {
            DB::table('finance_vendors')->updateOrInsert(
                ['vendor_id' => $financeVendor['vendor_id']],
                $financeVendor
            );
        }

        $this->command->info('Finance Vendor seeder completed: ' . count($financeVendors) . ' finance vendor records created/updated.');
    }
}

