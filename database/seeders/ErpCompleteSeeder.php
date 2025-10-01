<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ErpCompleteSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info(' Seeding ALL ERP tables...');

        // Seed all ERP tables in dependency order
        $this->seedErpStatus();
        $this->seedErpUnits();
        $this->seedErpProductTypes();
        $this->seedErpProductGroups();
        $this->seedErpBrands();
        $this->seedErpCustomers();
        $this->seedErpSuppliers();
        $this->seedErpForwarders();
        $this->seedErpSites();
        $this->seedErpStorages();
        $this->seedErpProducts();
        $this->seedErpStocks();
        $this->seedErpAnalyses();
        $this->seedErpAnalyseProperties();
        $this->seedErpAnalyseLines();
        $this->seedErpSamples();
        $this->seedErpEventHandlers();
        $this->seedErpStockHandlers();
        $this->seedErpOrdersIn();
        $this->seedErpOrdersOut();
        $this->seedErpOrderInLines();
        $this->seedErpOrderOutLines();

        $this->command->info('✅ ALL ERP tables seeded successfully!');
    }

    private function seedErpStatus(): void
    {
        $this->command->info('�� Seeding erp_status...');
        $statusData = [
            ['name' => 'Pending', 'code' => 'pending', 'sort_order' => 1, 'color' => 'yellow', 'description' => 'Initial status', 'is_active' => true],
            ['name' => 'Released', 'code' => 'released', 'sort_order' => 2, 'color' => 'blue', 'description' => 'Approved and ready', 'is_active' => true],
            ['name' => 'Active', 'code' => 'active', 'sort_order' => 3, 'color' => 'green', 'description' => 'Currently active', 'is_active' => true],
            ['name' => 'Finished', 'code' => 'finished', 'sort_order' => 4, 'color' => 'gray', 'description' => 'Completed', 'is_active' => true],
            ['name' => 'Archived', 'code' => 'archived', 'sort_order' => 5, 'color' => 'black', 'description' => 'Archived', 'is_active' => true],
        ];

        foreach ($statusData as $status) {
            DB::table('erp_status')->updateOrInsert(
                ['code' => $status['code']],
                array_merge($status, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpUnits(): void
    {
        $this->command->info('📏 Seeding erp_units...');
        $unitsData = [
            ['name' => 'Piece', 'symbol' => 'pcs', 'is_decimal' => false],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_decimal' => true],
            ['name' => 'Liter', 'symbol' => 'L', 'is_decimal' => true],
            ['name' => 'Meter', 'symbol' => 'm', 'is_decimal' => true],
            ['name' => 'Box', 'symbol' => 'box', 'is_decimal' => false],
        ];

        foreach ($unitsData as $unit) {
            DB::table('erp_units')->updateOrInsert(
                ['symbol' => $unit['symbol']],
                array_merge($unit, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpProductTypes(): void
    {
        $this->command->info('📦 Seeding erp_product_types...');
        $productTypesData = [
            ['name' => 'normal', 'description' => 'Standard discrete items that are counted individually'],
            ['name' => 'bulk', 'description' => 'Bulk materials measured by weight or volume'],
            ['name' => 'liquid', 'description' => 'Liquid materials measured by volume'],
            ['name' => 'service', 'description' => 'Services or intangible products'],
        ];

        foreach ($productTypesData as $productType) {
            DB::table('erp_product_types')->updateOrInsert(
                ['name' => $productType['name']],
                array_merge($productType, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpProductGroups(): void
    {
        $this->command->info('📁 Seeding erp_product_groups...');
        
        // Get product type IDs
        $bulkTypeId = DB::table('erp_product_types')->where('name', 'bulk')->first()->id;
        $normalTypeId = DB::table('erp_product_types')->where('name', 'normal')->first()->id;
        $liquidTypeId = DB::table('erp_product_types')->where('name', 'liquid')->first()->id;
        $serviceTypeId = DB::table('erp_product_types')->where('name', 'service')->first()->id;
        
        // Create 25+ product groups with erp_product_type_id
        $productGroups = [
            ['name' => 'Raw Materials', 'code' => 'RAW', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Basic materials used in production'],
            ['name' => 'Steel Products', 'code' => 'STEEL', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Steel materials and components'],
            ['name' => 'Aluminum Products', 'code' => 'ALUM', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Aluminum materials and components'],
            ['name' => 'Copper Products', 'code' => 'COPP', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Copper materials and components'],
            ['name' => 'Electronic Components', 'code' => 'ELEC', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electronic parts and components'],
            ['name' => 'Hand Tools', 'code' => 'HAND', 'erp_product_type_id' => $normalTypeId, 'description' => 'Manual tools and equipment'],
            ['name' => 'Power Tools', 'code' => 'POWER', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electric and pneumatic tools'],
            ['name' => 'Office Supplies', 'code' => 'OFF', 'erp_product_type_id' => $normalTypeId, 'description' => 'Office and stationery items'],
            ['name' => 'Fasteners', 'code' => 'FAST', 'erp_product_type_id' => $normalTypeId, 'description' => 'Screws, bolts, nuts, and fasteners'],
            ['name' => 'Pipes & Fittings', 'code' => 'PIPE', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Pipes, tubes, and fittings'],
            ['name' => 'Electrical Wire', 'code' => 'WIRE', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Electrical cables and wiring'],
            ['name' => 'Safety Equipment', 'code' => 'SAFE', 'erp_product_type_id' => $normalTypeId, 'description' => 'Safety gear and protective equipment'],
            ['name' => 'Measuring Tools', 'code' => 'MEAS', 'erp_product_type_id' => $normalTypeId, 'description' => 'Measuring instruments and tools'],
            ['name' => 'Cutting Tools', 'code' => 'CUT', 'erp_product_type_id' => $normalTypeId, 'description' => 'Cutting blades and tools'],
            ['name' => 'Lubricants', 'code' => 'LUBE', 'erp_product_type_id' => $liquidTypeId, 'description' => 'Oils, greases, and lubricants'],
            ['name' => 'Chemicals', 'code' => 'CHEM', 'erp_product_type_id' => $liquidTypeId, 'description' => 'Chemical compounds and substances'],
            ['name' => 'Adhesives', 'code' => 'ADH', 'erp_product_type_id' => $liquidTypeId, 'description' => 'Glues, tapes, and adhesives'],
            ['name' => 'Seals & Gaskets', 'code' => 'SEAL', 'erp_product_type_id' => $normalTypeId, 'description' => 'Sealing materials and gaskets'],
            ['name' => 'Bearings', 'code' => 'BEAR', 'erp_product_type_id' => $normalTypeId, 'description' => 'Ball bearings and roller bearings'],
            ['name' => 'Motors', 'code' => 'MOTOR', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electric motors and drives'],
            ['name' => 'Pumps', 'code' => 'PUMP', 'erp_product_type_id' => $normalTypeId, 'description' => 'Water pumps and fluid pumps'],
            ['name' => 'Valves', 'code' => 'VALVE', 'erp_product_type_id' => $normalTypeId, 'description' => 'Control valves and regulators'],
            ['name' => 'Sensors', 'code' => 'SENS', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electronic sensors and detectors'],
            ['name' => 'Controllers', 'code' => 'CTRL', 'erp_product_type_id' => $normalTypeId, 'description' => 'Control systems and PLCs'],
            ['name' => 'Cables', 'code' => 'CABLE', 'erp_product_type_id' => $bulkTypeId, 'description' => 'Data and power cables'],
            ['name' => 'Connectors', 'code' => 'CONN', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electrical connectors and terminals'],
            ['name' => 'Switches', 'code' => 'SW', 'erp_product_type_id' => $normalTypeId, 'description' => 'Electrical switches and relays'],
            ['name' => 'Fuses', 'code' => 'FUSE', 'erp_product_type_id' => $normalTypeId, 'description' => 'Fuses and circuit breakers'],
            ['name' => 'Lights', 'code' => 'LIGHT', 'erp_product_type_id' => $normalTypeId, 'description' => 'LEDs, bulbs, and lighting fixtures'],
            ['name' => 'Batteries', 'code' => 'BATT', 'erp_product_type_id' => $normalTypeId, 'description' => 'Batteries and power supplies']
        ];
        
        foreach ($productGroups as $group) {
            DB::table('erp_product_groups')->insert([
                'name' => $group['name'],
                'code' => $group['code'],
                'erp_product_type_id' => $group['erp_product_type_id'],
                'description' => $group['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get the raw materials group ID for parent relationships
        $rawMaterials = DB::table('erp_product_groups')->where('code', 'RAW')->first()->id;

        $finishedGoods = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Finished Goods',
            'code' => 'FIN',
            'erp_product_type_id' => $normalTypeId,
            'description' => 'Completed products ready for sale',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $tools = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Tools & Equipment',
            'code' => 'TOOL',
            'erp_product_type_id' => $normalTypeId,
            'description' => 'Tools and equipment used in operations',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $consumables = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Consumables',
            'code' => 'CONS',
            'erp_product_type_id' => $normalTypeId,
            'description' => 'Items consumed during operations',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $electronics = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Electronics',
            'code' => 'ELEC2',
            'erp_product_type_id' => $normalTypeId,
            'description' => 'Electronic devices and components',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create sub-groups for Raw Materials
        $metals = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Metals',
            'code' => 'METAL',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials,
            'description' => 'Metal materials and alloys',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $chemicals = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Chemicals',
            'code' => 'CHEM2',
            'erp_product_type_id' => $liquidTypeId,
            'parent_id' => $rawMaterials,
            'description' => 'Chemical compounds and substances',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $textiles = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Textiles',
            'code' => 'TEXT',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials,
            'description' => 'Fabric and textile materials',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $plastics = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Plastics',
            'code' => 'PLAST',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials,
            'description' => 'Plastic materials and resins',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create sub-groups for Finished Goods
        $furniture = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Furniture',
            'code' => 'FURN',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $finishedGoods,
            'description' => 'Furniture and fixtures',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $apparel = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Apparel',
            'code' => 'APP',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $finishedGoods,
            'description' => 'Clothing and accessories',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $automotive = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Automotive',
            'code' => 'AUTO',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $finishedGoods,
            'description' => 'Automotive parts and accessories',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create sub-groups for Tools & Equipment
        $handTools = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Hand Tools',
            'code' => 'HAND2',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $tools,
            'description' => 'Manual tools and instruments',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $powerTools = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Power Tools',
            'code' => 'POWER2',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $tools,
            'description' => 'Electric and pneumatic tools',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $machinery = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Machinery',
            'code' => 'MACH',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $tools,
            'description' => 'Large machinery and equipment',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create sub-groups for Consumables
        $officeSupplies = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Office Supplies',
            'code' => 'OFF2',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $consumables,
            'description' => 'Office and administrative supplies',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $safetyEquipment = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Safety Equipment',
            'code' => 'SAFE2',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $consumables,
            'description' => 'Safety and protective equipment',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cleaningSupplies = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Cleaning Supplies',
            'code' => 'CLEAN',
            'erp_product_type_id' => $normalTypeId,
            'parent_id' => $consumables,
            'description' => 'Cleaning and maintenance supplies',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create deeper nested groups
        $steel = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Steel',
            'code' => 'STEEL2',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $metals,
            'description' => 'Steel materials and products',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $aluminum = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Aluminum',
            'code' => 'ALUM2',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $metals,
            'description' => 'Aluminum materials and products',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $copper = DB::table('erp_product_groups')->insertGetId([
            'name' => 'Copper',
            'code' => 'COPP2',
            'erp_product_type_id' => $bulkTypeId,
            'parent_id' => $metals,
            'description' => 'Copper materials and products',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function seedErpBrands(): void
    {
        $this->command->info('🏷️ Seeding erp_brands...');
        
        // Use the ErpBrandSeeder to seed brands
        $this->call(ErpBrandSeeder::class);
    }

    private function seedErpCustomers(): void
    {
        $this->command->info('👥 Seeding erp_customers...');
        $customersData = [
            ['name' => 'Acme Corporation', 'identifier' => 'ACME001', 'comment' => 'Major industrial client', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Tech Solutions Ltd', 'identifier' => 'TECH002', 'comment' => 'Technology services company', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Global Industries', 'identifier' => 'GLOB003', 'comment' => 'International trading company', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Local Distributor', 'identifier' => 'LOCAL04', 'comment' => 'Regional distribution partner', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Startup Ventures', 'identifier' => 'START05', 'comment' => 'New startup client', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($customersData as $customer) {
            DB::table('erp_customers')->updateOrInsert(
                ['identifier' => $customer['identifier']],
                array_merge($customer, ['json' => json_encode(['priority' => 'medium']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpSuppliers(): void
    {
        $this->command->info('🏭 Seeding erp_suppliers...');
        $suppliersData = [
            ['name' => 'Industrial Supply Co', 'identifier' => 'IND001', 'comment' => 'Primary industrial supplier', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Tech Components Ltd', 'identifier' => 'TECH01', 'comment' => 'Electronic components supplier', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Raw Materials Inc', 'identifier' => 'RAW001', 'comment' => 'Raw materials supplier', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Chemical Solutions', 'identifier' => 'CHEM01', 'comment' => 'Chemical products supplier', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Equipment Rentals', 'identifier' => 'RENT01', 'comment' => 'Equipment rental company', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($suppliersData as $supplier) {
            DB::table('erp_suppliers')->updateOrInsert(
                ['identifier' => $supplier['identifier']],
                array_merge($supplier, ['json' => json_encode(['rating' => 'A', 'category' => 'Industrial']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpForwarders(): void
    {
        $this->command->info('🚚 Seeding erp_forwarders...');
        $forwardersData = [
            ['name' => 'Global Logistics', 'identifier' => 'LOG001', 'comment' => 'International shipping', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Local Transport', 'identifier' => 'LOC001', 'comment' => 'Local delivery service', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Express Shipping', 'identifier' => 'EXP001', 'comment' => 'Express delivery', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($forwardersData as $forwarder) {
            DB::table('erp_forwarders')->updateOrInsert(
                ['identifier' => $forwarder['identifier']],
                array_merge($forwarder, ['json' => json_encode(['service_type' => 'shipping']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpSites(): void
    {
        $this->command->info('🏢 Seeding erp_sites...');
        $sitesData = [
            ['name' => 'Main Warehouse', 'identifier' => 'MAIN01', 'comment' => 'Primary distribution center', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Production Facility', 'identifier' => 'PROD01', 'comment' => 'Main production site', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Office Complex', 'identifier' => 'OFF01', 'comment' => 'Administrative headquarters', 'is_active' => true, 'is_locked' => false],
            ['name' => 'Storage Facility', 'identifier' => 'STOR01', 'comment' => 'Additional storage', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($sitesData as $site) {
            DB::table('erp_sites')->updateOrInsert(
                ['identifier' => $site['identifier']],
                array_merge($site, ['json' => json_encode(['type' => 'Warehouse', 'capacity' => '10000']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpStorages(): void
    {
        $this->command->info(' Seeding erp_storages...');
        $siteIds = DB::table('erp_sites')->pluck('id', 'identifier')->toArray();
        $unitIds = DB::table('erp_units')->pluck('id', 'symbol')->toArray();
        
        $storagesData = [
            ['code' => 'A1B2', 'type' => 'rack', 'site_id' => $siteIds['MAIN01'] ?? 1, 'unit_id' => $unitIds['pcs'] ?? 1, 'max_units' => 1000.0000, 'comment' => 'Steel products storage', 'is_active' => true, 'is_locked' => false],
            ['code' => 'C3D4', 'type' => 'rack', 'site_id' => $siteIds['PROD01'] ?? 2, 'unit_id' => $unitIds['pcs'] ?? 1, 'max_units' => 500.0000, 'comment' => 'Electronics storage', 'is_active' => true, 'is_locked' => false],
            ['code' => 'E5F6', 'type' => 'bin', 'site_id' => $siteIds['MAIN01'] ?? 1, 'unit_id' => $unitIds['kg'] ?? 4, 'max_units' => 2000.0000, 'comment' => 'General storage', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($storagesData as $storage) {
            DB::table('erp_storages')->updateOrInsert(
                ['code' => $storage['code']],
                array_merge($storage, ['json' => json_encode(['location' => 'A1-B2']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpProducts(): void
    {
        $this->command->info('��️ Seeding erp_products...');
        $unitIds = DB::table('erp_units')->pluck('id', 'symbol')->toArray();
        $productTypeIds = DB::table('erp_product_types')->pluck('id', 'name')->toArray();
        $productGroupIds = DB::table('erp_product_groups')->pluck('id', 'code')->toArray();
        $brandIds = DB::table('erp_brands')->pluck('id', 'identifier')->toArray();
        
        $productsData = \Database\Seeders\ErpProductsData::getProductsData($unitIds, $productTypeIds, $productGroupIds, $brandIds);

        foreach ($productsData as $product) {
            $jsonData = [];
            if (str_contains($product['name'], 'Steel')) {
                $jsonData = ['material' => 'Steel', 'grade' => 'A36'];
            } elseif (str_contains($product['name'], 'Aluminum')) {
                $jsonData = ['material' => 'Aluminum', 'grade' => '6061'];
            } elseif (str_contains($product['name'], 'Copper')) {
                $jsonData = ['material' => 'Copper', 'purity' => '99.9%'];
            } elseif (str_contains($product['name'], 'Electronic') || str_contains($product['name'], 'Arduino') || str_contains($product['name'], 'Raspberry')) {
                $jsonData = ['category' => 'Electronics', 'voltage' => '5V'];
            } elseif (str_contains($product['name'], 'Chemical') || str_contains($product['name'], 'Acid') || str_contains($product['name'], 'Ethanol') || str_contains($product['name'], 'Acetone')) {
                $jsonData = ['hazard_class' => 'Corrosive', 'storage' => 'Cool Dry Place'];
            } elseif (str_contains($product['name'], 'Service')) {
                $jsonData = ['category' => 'Service', 'billing' => 'Hourly'];
            } else {
                $jsonData = ['category' => 'General', 'supplier' => 'Default'];
            }

            DB::table('erp_products')->updateOrInsert(
                ['identifier' => $product['identifier']],
                array_merge($product, ['json' => json_encode($jsonData), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpStocks(): void
    {
        $this->command->info('�� Seeding erp_stocks...');
        $productIds = DB::table('erp_products')->pluck('id', 'identifier')->toArray();
        $storageIds = DB::table('erp_storages')->pluck('id', 'code')->toArray();
        
        $stocksData = [
            ['storage_id' => $storageIds['A1B2'] ?? 1, 'product_id' => $productIds['STEEL001'] ?? 1, 'qty' => 1000.0000, 'comment' => 'Steel rods stock', 'is_active' => true, 'is_locked' => false],
            ['storage_id' => $storageIds['C3D4'] ?? 2, 'product_id' => $productIds['ELEC001'] ?? 2, 'qty' => 500.0000, 'comment' => 'Electronic components stock', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($stocksData as $stock) {
            DB::table('erp_stocks')->insert(array_merge($stock, ['json' => json_encode(['location' => 'A1-B2']), 'created_at' => now(), 'updated_at' => now()]));
        }
    }

    private function seedErpAnalyses(): void
    {
        $this->command->info(' Seeding erp_analyses...');
        $analysesData = [
            ['name' => 'Quality Control Analysis', 'description' => 'Standard quality control testing', 'is_active' => true],
            ['name' => 'Material Testing', 'description' => 'Material composition analysis', 'is_active' => true],
            ['name' => 'Performance Testing', 'description' => 'Product performance evaluation', 'is_active' => true],
        ];

        foreach ($analysesData as $analysis) {
            DB::table('erp_analyses')->updateOrInsert(
                ['name' => $analysis['name']],
                array_merge($analysis, ['json' => json_encode(['type' => 'quality']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpAnalyseProperties(): void
    {
        $this->command->info('�� Seeding erp_analyse_properties...');
        $propertiesData = [
            ['name' => 'Moisture Content', 'unit' => '%', 'data_type' => 'number', 'is_active' => true],
            ['name' => 'pH Level', 'unit' => 'pH', 'data_type' => 'number', 'is_active' => true],
            ['name' => 'Temperature', 'unit' => '°C', 'data_type' => 'number', 'is_active' => true],
            ['name' => 'Purity', 'unit' => '%', 'data_type' => 'number', 'is_active' => true],
            ['name' => 'Viscosity', 'unit' => 'cP', 'data_type' => 'number', 'is_active' => true],
        ];

        foreach ($propertiesData as $property) {
            DB::table('erp_analyse_properties')->updateOrInsert(
                ['name' => $property['name']],
                array_merge($property, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpAnalyseLines(): void
    {
        $this->command->info(' Seeding erp_analyse_lines...');
        $analysisIds = DB::table('erp_analyses')->pluck('id', 'name')->toArray();
        $propertyIds = DB::table('erp_analyse_properties')->pluck('id', 'name')->toArray();
        
        $linesData = [
            ['analysis_id' => $analysisIds['Quality Control Analysis'] ?? 1, 'property_id' => $propertyIds['Moisture Content'] ?? 1, 'value' => '5.2', 'comment' => 'Moisture test result'],
            ['analysis_id' => $analysisIds['Material Testing'] ?? 2, 'property_id' => $propertyIds['Purity'] ?? 4, 'value' => '99.5', 'comment' => 'Purity test result'],
        ];

        foreach ($linesData as $line) {
            DB::table('erp_analyse_lines')->insert(array_merge($line, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    private function seedErpSamples(): void
    {
        $this->command->info('🧪 Seeding erp_samples...');
        $samplesData = [
            ['name' => 'Sample A', 'description' => 'Quality control sample', 'is_active' => true],
            ['name' => 'Sample B', 'description' => 'Material testing sample', 'is_active' => true],
            ['name' => 'Sample C', 'description' => 'Performance test sample', 'is_active' => true],
        ];

        foreach ($samplesData as $sample) {
            DB::table('erp_samples')->updateOrInsert(
                ['name' => $sample['name']],
                array_merge($sample, ['json' => json_encode(['type' => 'quality']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpEventHandlers(): void
    {
        $this->command->info('⚡ Seeding erp_event_handlers...');
        $handlersData = [
            [
                'module' => 'inventory',
                'action' => 'on_receive',
                'start_plan' => now()->subDays(1),
                'end_plan' => now()->addDays(1),
                'start_actual' => now()->subHours(2),
                'end_actual' => now()->addHours(2),
                'due' => now()->addDays(1),
                'comment' => 'Handle incoming goods',
                'json' => json_encode(['type' => 'system', 'priority' => 'high'])
            ],
            [
                'module' => 'inventory',
                'action' => 'on_ship',
                'start_plan' => now()->subDays(2),
                'end_plan' => now()->addDays(2),
                'start_actual' => now()->subHours(1),
                'end_actual' => now()->addHours(1),
                'due' => now()->addDays(2),
                'comment' => 'Handle outgoing shipments',
                'json' => json_encode(['type' => 'system', 'priority' => 'medium'])
            ],
            [
                'module' => 'orders',
                'action' => 'on_update',
                'start_plan' => now()->subDays(3),
                'end_plan' => now()->addDays(3),
                'start_actual' => now()->subMinutes(30),
                'end_actual' => now()->addMinutes(30),
                'due' => now()->addDays(3),
                'comment' => 'Handle status updates',
                'json' => json_encode(['type' => 'system', 'priority' => 'low'])
            ],
        ];

        foreach ($handlersData as $handler) {
            DB::table('erp_event_handlers')->insert(array_merge($handler, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    private function seedErpStockHandlers(): void
    {
        $this->command->info('📦 Seeding erp_stock_handlers...');
        $handlersData = [
            ['name' => 'update_stock', 'description' => 'Update stock levels', 'is_active' => true],
            ['name' => 'reduce_stock', 'description' => 'Reduce stock on shipment', 'is_active' => true],
            ['name' => 'add_stock', 'description' => 'Add stock on receipt', 'is_active' => true],
        ];

        foreach ($handlersData as $handler) {
            DB::table('erp_stock_handlers')->updateOrInsert(
                ['name' => $handler['name']],
                array_merge($handler, ['json' => json_encode(['type' => 'inventory']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpOrdersIn(): void
    {
        $this->command->info('�� Seeding erp_orders_in...');
        $supplierIds = DB::table('erp_suppliers')->pluck('id', 'identifier')->toArray();
        $siteIds = DB::table('erp_sites')->pluck('id', 'identifier')->toArray();
        $statusIds = DB::table('erp_status')->pluck('id', 'code')->toArray();
        
        $ordersData = [
            ['ordernr' => 'IN-2025-001', 'supplier_id' => $supplierIds['IND001'] ?? 1, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['pending'] ?? 1, 'comment' => 'Steel rods order', 'is_active' => true, 'is_locked' => false],
            ['ordernr' => 'IN-2025-002', 'supplier_id' => $supplierIds['TECH01'] ?? 2, 'site_id' => $siteIds['PROD01'] ?? 2, 'status_id' => $statusIds['released'] ?? 2, 'comment' => 'Electronic components order', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($ordersData as $order) {
            DB::table('erp_orders_in')->updateOrInsert(
                ['ordernr' => $order['ordernr']],
                array_merge($order, ['eventHandlers' => json_encode(['on_receive' => 'update_stock']), 'json' => json_encode(['priority' => 'high']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpOrdersOut(): void
    {
        $this->command->info('📤 Seeding erp_orders_out...');
        $customerIds = DB::table('erp_customers')->pluck('id', 'identifier')->toArray();
        $siteIds = DB::table('erp_sites')->pluck('id', 'identifier')->toArray();
        $statusIds = DB::table('erp_status')->pluck('id', 'code')->toArray();
        
        $ordersData = [
            ['ordernr' => 'OUT-2025-001', 'customer_id' => $customerIds['ACME001'] ?? 1, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['active'] ?? 3, 'comment' => 'Steel rods delivery', 'is_active' => true, 'is_locked' => false],
            ['ordernr' => 'OUT-2025-002', 'customer_id' => $customerIds['TECH002'] ?? 2, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['pending'] ?? 1, 'comment' => 'Electronic components delivery', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($ordersData as $order) {
            DB::table('erp_orders_out')->updateOrInsert(
                ['ordernr' => $order['ordernr']],
                array_merge($order, ['eventHandlers' => json_encode(['on_ship' => 'reduce_stock']), 'json' => json_encode(['priority' => 'high']), 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedErpOrderInLines(): void
    {
        $this->command->info('📋 Seeding erp_order_in_lines...');
        $orderIds = DB::table('erp_orders_in')->pluck('id', 'ordernr')->toArray();
        $productIds = DB::table('erp_products')->pluck('id', 'identifier')->toArray();
        $siteIds = DB::table('erp_sites')->pluck('id', 'identifier')->toArray();
        $statusIds = DB::table('erp_status')->pluck('id', 'code')->toArray();
        
        $linesData = [
            ['order_in_id' => $orderIds['IN-2025-001'] ?? 1, 'product_id' => $productIds['STEEL001'] ?? 1, 'qty' => 1000.0000, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['pending'] ?? 1, 'comment' => 'Steel rods - 1000kg', 'is_active' => true, 'is_locked' => false],
            ['order_in_id' => $orderIds['IN-2025-002'] ?? 2, 'product_id' => $productIds['ELEC001'] ?? 2, 'qty' => 500.0000, 'site_id' => $siteIds['PROD01'] ?? 2, 'status_id' => $statusIds['released'] ?? 2, 'comment' => 'Electronic components - 500pcs', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($linesData as $line) {
            DB::table('erp_order_in_lines')->insert(array_merge($line, ['eventHandlers' => json_encode(['on_receive' => 'update_stock']), 'stockHandler' => json_encode(['location' => 'A1-B2']), 'json' => json_encode(['specifications' => 'Grade A36']), 'created_at' => now(), 'updated_at' => now()]));
        }
    }

    private function seedErpOrderOutLines(): void
    {
        $this->command->info('📋 Seeding erp_order_out_lines...');
        $orderIds = DB::table('erp_orders_out')->pluck('id', 'ordernr')->toArray();
        $productIds = DB::table('erp_products')->pluck('id', 'identifier')->toArray();
        $siteIds = DB::table('erp_sites')->pluck('id', 'identifier')->toArray();
        $statusIds = DB::table('erp_status')->pluck('id', 'code')->toArray();
        
        $linesData = [
            ['order_out_id' => $orderIds['OUT-2025-001'] ?? 1, 'product_id' => $productIds['STEEL001'] ?? 1, 'qty' => 500.0000, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['active'] ?? 3, 'comment' => 'Steel rods - 500kg outgoing', 'is_active' => true, 'is_locked' => false],
            ['order_out_id' => $orderIds['OUT-2025-002'] ?? 2, 'product_id' => $productIds['ELEC001'] ?? 2, 'qty' => 250.0000, 'site_id' => $siteIds['MAIN01'] ?? 1, 'status_id' => $statusIds['pending'] ?? 1, 'comment' => 'Electronic components - 250pcs outgoing', 'is_active' => true, 'is_locked' => false],
        ];

        foreach ($linesData as $line) {
            DB::table('erp_order_out_lines')->insert(array_merge($line, ['eventHandlers' => json_encode(['on_ship' => 'reduce_stock']), 'stockHandler' => json_encode(['location' => 'A1-B2']), 'json' => json_encode(['specifications' => 'Grade A36']), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}