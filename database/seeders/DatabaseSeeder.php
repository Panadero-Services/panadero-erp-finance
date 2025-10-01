<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This seeder includes ALL available seeders in the correct dependency order.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding Process...');

        // ===== STEP 1: CREATE CORE ROLES =====
        $this->command->info('📋 Creating core roles...');
        $memberRole = Role::create(['name' => 'member']);
        $editorRole = Role::create(['name' => 'editor']);
        $adminRole = Role::create(['name' => 'admin']);
        $developerRole = Role::create(['name' => 'developer']);
        $masterRole = Role::create(['name' => 'master']);

        // ===== STEP 2: CORE FOUNDATION SEEDERS =====
        $this->command->info('🏗️  Seeding foundation data...');
        $this->call([
            ProviderSeeder::class,           // External providers
            UserSeeder::class,               // System users (MUST be early for foreign keys)
            PermissionSeeder::class,         // Permissions system
        ]);

        // ===== STEP 3: BASIC CONTENT STRUCTURE =====
        $this->command->info('📝 Seeding content structure...');
        $this->call([
            CategorySeeder::class,           // Content categories
            PostTypeSeeder::class,           // Post types
            TagSeeder::class,                // Content tags
            PostSeeder::class,               // Blog posts and content
        ]);

        // ===== STEP 4: WEB3 & BLOCKCHAIN DATA =====
        $this->command->info('⛓️  Seeding Web3 and blockchain data...');
        $this->call([
            Web3ChainSeeder::class,          // Blockchain networks
            Web3TypeSeeder::class,           // Web3 transaction types
            Web3ProjectSeeder::class,        // Web3 projects
            Web3RecordSeeder::class,         // Web3 transaction records
        ]);

        // ===== STEP 5: PROJECT MANAGEMENT =====
        $this->command->info('📊 Seeding project management data...');
        $this->call([
            ProjectSeeder::class,            // Projects
            FeatureSeeder::class,            // Project features
            FutureSeeder::class,             // Future roadmap items
            BusinessServiceSeeder::class,    // Business services
        ]);

        // ===== STEP 6: CONTENT MANAGEMENT =====
        $this->command->info('📄 Seeding content management...');
        $this->call([
            PageSeeder::class,               // CMS pages
            SectionsSeeder::class,           // Page sections
            UpdatePagesSeeder::class,        // Page updates
            FuturesPageSeeder::class,        // Future pages
        ]);

        // ===== STEP 7: DATA MANAGEMENT =====
        $this->command->info('📈 Seeding data management...');
        $this->call([
            StateDatasetSeeder::class,       // State datasets
            RawDatasetSeeder::class,         // Raw datasets
            LogSeeder::class,                // System logs
        ]);

        // ===== STEP 8: GAMING SYSTEM =====
        $this->command->info('🎮 Seeding gaming system...');
        $this->call([
            GameWorldSeeder::class,          // Game worlds and gaming data
        ]);

        // ===== STEP 9: I1 BUSINESS SYSTEM =====
        $this->command->info('🏭 Seeding I1 business system...');
        $this->call([
            I1TablesDemoSeeder::class,       // I1 core tables (countries, customers, etc.)
            I1OrdersSeeder::class,           // I1 orders
        ]);

        // ===== STEP 10: INVENTORY & COMMERCE =====
        $this->command->info('📦 Seeding inventory and commerce...');
        $this->call([
            // Locations and storage
            SiteSeeder::class,               // Sites/locations
            WarehouseSeeder::class,          // Warehouses
            
            // Products and inventory
            ArticleGroupSeeder::class,       // Article groups
            ArticleSeeder::class,            // Articles/products
            InventorySeeder::class,          // Inventory management
            
            // Sales and orders
            OrderSeeder::class,              // Orders
            OrderLineSeeder::class,          // Order line items
            SalesDetailSeeder::class,        // Sales details
            
            // Rentals
            RentalSeeder::class,             // Rental management
            RentalDetailSeeder::class,       // Rental details
            
            // ERP Inventory System
            InventoryDatabaseSeeder::class,     // ERP Inventory system
        ]);

        // ===== STEP 11: ERP LOOKUP DATA =====
        $this->command->info('🏭 Seeding ERP lookup data...');
        $this->call([
            ErpLookupDataSeeder::class,      // ERP status, units, product types, analyse properties
        ]);

        // ===== STEP 11.5: ERP COMPLETE DATA =====
        $this->command->info(' Seeding ALL ERP data...');
        $this->call([
            ErpCompleteSeeder::class,        // ALL ERP tables with complete data
        ]);

        // ===== STEP 12: FINANCE CORE SYSTEM =====
        $this->command->info('💰 Seeding finance core system...');
        $this->call([
            FinanceCoreSeeder::class,        // Finance accounts, chart of accounts
            FinanceInvoiceSeeder::class,     // Finance invoices
            FinanceDemoSeeder::class,        // Finance demo data
        ]);

        // ===== STEP 13: HR MANAGEMENT SYSTEM =====
        $this->command->info('👥 Seeding HR management system...');
        
        // Load HR seeders directly
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrDatabaseSeeder.php');
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrDepartmentSeeder.php');
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrEmployeeSeeder.php');
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrVacancySeeder.php');
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrApplicationSeeder.php');
        require_once base_path('packages/panadero-erp-hr/database/seeders/HrPerformanceReviewSeeder.php');
        
        $this->call([
            \Panadero\Erp\Hr\Database\Seeders\HrDatabaseSeeder::class,  // Complete HR system
        ]);

        // ===== STEP 14: COMPLIANCE MANAGEMENT SYSTEM =====
        $this->command->info('🛡️  Seeding compliance management system...');
        
        // Load compliance seeders directly
        require_once base_path('packages/panadero-erp-compliance/database/seeders/ComplianceDatabaseSeeder.php');
        require_once base_path('packages/panadero-erp-compliance/database/seeders/CompliancePolicySeeder.php');
        require_once base_path('packages/panadero-erp-compliance/database/seeders/ComplianceAuditSeeder.php');
        require_once base_path('packages/panadero-erp-compliance/database/seeders/ComplianceRiskSeeder.php');
        require_once base_path('packages/panadero-erp-compliance/database/seeders/ComplianceReportSeeder.php');
        require_once base_path('packages/panadero-erp-compliance/database/seeders/ComplianceRCASeeder.php');
        
        $this->call([
            \Panadero\ErpCompliance\Database\Seeders\ComplianceDatabaseSeeder::class,  // Complete compliance system
        ]);

        // ===== STEP 15: VENDOR MANAGEMENT =====
        $this->command->info('🏢 Seeding vendor management...');
        $this->call([
            VendorSeeder::class,             // Core vendor entities (shared)
            FinanceVendorSeeder::class,      // Finance-specific vendor extensions
        ]);

        // ===== STEP 16: USER ROLE ASSIGNMENTS =====
        $this->command->info('👥 Assigning user roles...');
        
        // Get all users and roles
        $users = User::all();
        $roles = Role::all();

        // First, give member role to all users
        foreach ($users as $user) {
            $user->roles()->attach($memberRole);
        }

        // Give all roles to primary admin users
        $adminEmails = ['lbakker@me.com', 'jawsome.orbit@gmail.com'];
        
        foreach ($adminEmails as $email) {
            $adminUser = User::where('email', $email)->first();
            if ($adminUser) {
                // Detach existing roles first to avoid duplicates
                $adminUser->roles()->detach();
                // Attach all roles
                $adminUser->roles()->attach($roles);
                $this->command->info("🔑 Granted all roles to: {$email}");
            }
        }

        // ===== STEP 17: TEAM MANAGEMENT =====
        $this->command->info('🏆 Setting up team management...');
        
        // Create demo team
        $demoTeam = \App\Models\Team::create([
            'name' => 'demo', 
            'id' => 1, 
            'user_id' => 1, 
            'personal_team' => 1
        ]);

        // Set current team for all users
        $users = User::all();
        foreach ($users as $user) {
            $user->update(['current_team_id' => 1]);
        }

        // ===== STEP 18: FINALIZATION =====
        $this->command->info('✨ Creating demo comments...');
        Comment::factory()->count(50)->create();

        // ===== COMPLETION SUMMARY =====
        $this->showCompletionSummary();
    }

    /**
     * Display completion summary with statistics
     */
    private function showCompletionSummary(): void
    {
        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 DATABASE SEEDING COMPLETED SUCCESSFULLY!');
        $this->command->info('🎉 ========================================');
        $this->command->info('');

        // Count records in key tables
        $counts = [
            'Users' => User::count(),
            'Roles' => Role::count(),
            'Posts' => \App\Models\Post::count(),
            'Projects' => \App\Models\Project::count(),
            'Vendors' => \App\Models\Vendor::count(),
            'Finance Vendors' => \App\Models\FinanceVendor::count(),
            'Finance Accounts' => \DB::table('finance_accounts')->count(),
            'HR Departments' => \DB::table('hr_departments')->count(),
            'HR Employees' => \DB::table('hr_employees')->count(),
            'HR Vacancies' => \DB::table('hr_vacancies')->count(),
            'HR Applications' => \DB::table('hr_applications')->count(),
            'Compliance Policies' => \DB::table('compliance_policies')->count(),
            'Compliance Audits' => \DB::table('compliance_audits')->count(),
            'Compliance Risks' => \DB::table('compliance_risks')->count(),
            'Compliance Reports' => \DB::table('compliance_reports')->count(),
            'Compliance RCA' => \DB::table('compliance_rca')->count(),
        ];

        $this->command->info('📊 SEEDING STATISTICS:');
        foreach ($counts as $table => $count) {
            $this->command->info("   • {$table}: {$count} records");
        }

        $this->command->info('');
        $this->command->info('✅ All seeders have been executed in the correct dependency order.');
        $this->command->info('🚀 Your application is ready for testing!');
        $this->command->info('');
    }
}