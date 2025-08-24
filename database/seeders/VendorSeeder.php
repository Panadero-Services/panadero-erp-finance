<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\User;
use Carbon\Carbon;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating vendor records...');

        // Get a user for approval fields
        $approverUser = User::first();
        $approverUserId = $approverUser ? $approverUser->id : 1;

        $vendors = [
            [
                'vendor_code' => 'ACME001',
                'name' => 'ACME Corporation',
                'legal_name' => 'ACME Corporation LLC',
                'contact_person' => 'John Smith',
                'email' => 'procurement@acme.corp',
                'phone' => '+1-555-0123',
                'website' => 'https://www.acme.corp',
                'address' => '123 Business Park Drive',
                'city' => 'New York',
                'state_province' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
                'tax_id' => '12-3456789',
                'registration_number' => 'LLC-NY-123456',
                'vendor_type' => 'supplier',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Manufacturing',
                    'certifications' => ['ISO 9001', 'ISO 14001'],
                    'credit_rating' => 'A+'
                ]),
                'external_id' => 'EXT-ACME-001',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(30),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'vendor_code' => 'TECH002',
                'name' => 'TechSolutions Inc',
                'legal_name' => 'TechSolutions Incorporated',
                'contact_person' => 'Sarah Johnson',
                'email' => 'vendors@techsolutions.com',
                'phone' => '+1-555-0456',
                'website' => 'https://www.techsolutions.com',
                'address' => '456 Tech Boulevard',
                'city' => 'San Francisco',
                'state_province' => 'CA',
                'postal_code' => '94105',
                'country' => 'US',
                'tax_id' => '98-7654321',
                'registration_number' => 'INC-CA-987654',
                'vendor_type' => 'service_provider',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Information Technology',
                    'specialties' => ['Cloud Services', 'Software Development', 'IT Consulting'],
                    'credit_rating' => 'A'
                ]),
                'external_id' => 'EXT-TECH-002',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(25),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'vendor_code' => 'CLEAN003',
                'name' => 'CleanPro Services',
                'legal_name' => 'CleanPro Professional Services Ltd',
                'contact_person' => 'Mike Wilson',
                'email' => 'info@cleanpro.com',
                'phone' => '+1-555-0789',
                'website' => 'https://www.cleanpro.com',
                'address' => '789 Service Street',
                'city' => 'Chicago',
                'state_province' => 'IL',
                'postal_code' => '60601',
                'country' => 'US',
                'tax_id' => '45-6789012',
                'registration_number' => 'LTD-IL-456789',
                'vendor_type' => 'service_provider',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Facility Services',
                    'services' => ['Office Cleaning', 'Janitorial', 'Maintenance'],
                    'credit_rating' => 'B+'
                ]),
                'external_id' => 'EXT-CLEAN-003',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(20),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'vendor_code' => 'CONST004',
                'name' => 'BuildRight Construction',
                'legal_name' => 'BuildRight Construction Company',
                'contact_person' => 'Robert Brown',
                'email' => 'contracts@buildright.com',
                'phone' => '+1-555-0321',
                'website' => 'https://www.buildright.com',
                'address' => '321 Construction Ave',
                'city' => 'Houston',
                'state_province' => 'TX',
                'postal_code' => '77001',
                'country' => 'US',
                'tax_id' => '78-9012345',
                'registration_number' => 'CORP-TX-789012',
                'vendor_type' => 'contractor',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Construction',
                    'specialties' => ['Commercial Building', 'Renovation', 'Project Management'],
                    'credit_rating' => 'A-'
                ]),
                'external_id' => 'EXT-CONST-004',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(15),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'vendor_code' => 'CONSULT005',
                'name' => 'Strategic Advisors Group',
                'legal_name' => 'Strategic Advisors Group LLC',
                'contact_person' => 'Dr. Emily Davis',
                'email' => 'consulting@strategicadvisors.com',
                'phone' => '+1-555-0654',
                'website' => 'https://www.strategicadvisors.com',
                'address' => '654 Executive Plaza',
                'city' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02101',
                'country' => 'US',
                'tax_id' => '23-4567890',
                'registration_number' => 'LLC-MA-234567',
                'vendor_type' => 'consultant',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Management Consulting',
                    'expertise' => ['Strategic Planning', 'Process Improvement', 'Change Management'],
                    'credit_rating' => 'A+'
                ]),
                'external_id' => 'EXT-CONSULT-005',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(10),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'vendor_code' => 'PEND006',
                'name' => 'Pending Vendor Ltd',
                'legal_name' => 'Pending Vendor Limited',
                'contact_person' => 'Alex Johnson',
                'email' => 'contact@pendingvendor.com',
                'phone' => '+1-555-0987',
                'website' => 'https://www.pendingvendor.com',
                'address' => '987 Prospect Street',
                'city' => 'Denver',
                'state_province' => 'CO',
                'postal_code' => '80201',
                'country' => 'US',
                'tax_id' => '56-7890123',
                'registration_number' => 'LTD-CO-567890',
                'vendor_type' => 'supplier',
                'status' => 'pending',
                'metadata' => json_encode([
                    'industry' => 'Office Supplies',
                    'products' => ['Stationery', 'Office Equipment', 'Furniture'],
                    'credit_rating' => 'Not Rated'
                ]),
                'external_id' => 'EXT-PEND-006',
                'is_approved' => false,
                'approved_at' => null,
                'approved_by' => null,
                'is_active' => false,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'vendor_code' => 'GLOBAL007',
                'name' => 'Global Logistics Partners',
                'legal_name' => 'Global Logistics Partners B.V.',
                'contact_person' => 'Hans Mueller',
                'email' => 'partners@globallogistics.eu',
                'phone' => '+31-20-1234567',
                'website' => 'https://www.globallogistics.eu',
                'address' => 'Hoofdstraat 123',
                'city' => 'Amsterdam',
                'state_province' => 'Noord-Holland',
                'postal_code' => '1012 AB',
                'country' => 'NL',
                'tax_id' => 'NL123456789B01',
                'registration_number' => 'KVK-12345678',
                'vendor_type' => 'service_provider',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Logistics & Transportation',
                    'services' => ['Freight Forwarding', 'Customs Clearance', 'Warehousing'],
                    'credit_rating' => 'A'
                ]),
                'external_id' => 'EXT-GLOBAL-007',
                'is_approved' => true,
                'approved_at' => Carbon::now()->subDays(45),
                'approved_by' => $approverUserId,
                'is_active' => true,
                'is_locked' => false,
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendors')->updateOrInsert(
                ['vendor_code' => $vendor['vendor_code']],
                $vendor
            );
        }

        $this->command->info('Vendor seeder completed: ' . count($vendors) . ' vendors created/updated.');
    }
}
