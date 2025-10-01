<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ErpProductGroup;

class ErpProductGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get product type IDs
        $bulkTypeId = \App\Models\ErpProductType::where('name', 'bulk')->first()->id;
        $normalTypeId = \App\Models\ErpProductType::where('name', 'normal')->first()->id;
        $liquidTypeId = \App\Models\ErpProductType::where('name', 'liquid')->first()->id;
        $serviceTypeId = \App\Models\ErpProductType::where('name', 'service')->first()->id;

        // BULK MATERIALS - Raw materials sold by weight/volume
        $rawMaterials = ErpProductGroup::create([
            'name' => 'Raw Materials',
            'code' => 'RAW',
            'product_type_id' => $bulkTypeId,
            'description' => 'Basic materials used in production',
            'is_active' => true
        ]);

        $metals = ErpProductGroup::create([
            'name' => 'Metals',
            'code' => 'METAL',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials->id,
            'description' => 'Metal materials and alloys',
            'is_active' => true
        ]);

        $chemicals = ErpProductGroup::create([
            'name' => 'Chemicals',
            'code' => 'CHEM',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials->id,
            'description' => 'Chemical compounds and substances',
            'is_active' => true
        ]);

        $textiles = ErpProductGroup::create([
            'name' => 'Textiles',
            'code' => 'TEXT',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials->id,
            'description' => 'Fabric and textile materials',
            'is_active' => true
        ]);

        $plastics = ErpProductGroup::create([
            'name' => 'Plastics',
            'code' => 'PLAST',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $rawMaterials->id,
            'description' => 'Plastic materials and compounds',
            'is_active' => true
        ]);

        // NORMAL ITEMS - Discrete items sold by piece
        $electronics = ErpProductGroup::create([
            'name' => 'Electronics',
            'code' => 'ELEC',
            'product_type_id' => $normalTypeId,
            'description' => 'Electronic devices and components',
            'is_active' => true
        ]);

        $tools = ErpProductGroup::create([
            'name' => 'Tools & Equipment',
            'code' => 'TOOL',
            'product_type_id' => $normalTypeId,
            'description' => 'Tools and equipment used in operations',
            'is_active' => true
        ]);

        $furniture = ErpProductGroup::create([
            'name' => 'Furniture',
            'code' => 'FURN',
            'product_type_id' => $normalTypeId,
            'description' => 'Furniture and fixtures',
            'is_active' => true
        ]);

        $apparel = ErpProductGroup::create([
            'name' => 'Apparel',
            'code' => 'APP',
            'product_type_id' => $normalTypeId,
            'description' => 'Clothing and accessories',
            'is_active' => true
        ]);

        $automotive = ErpProductGroup::create([
            'name' => 'Automotive',
            'code' => 'AUTO',
            'product_type_id' => $normalTypeId,
            'description' => 'Automotive parts and accessories',
            'is_active' => true
        ]);

        $machinery = ErpProductGroup::create([
            'name' => 'Machinery',
            'code' => 'MACH',
            'product_type_id' => $normalTypeId,
            'description' => 'Large machinery and equipment',
            'is_active' => true
        ]);

        $officeSupplies = ErpProductGroup::create([
            'name' => 'Office Supplies',
            'code' => 'OFF',
            'product_type_id' => $normalTypeId,
            'description' => 'Office and administrative supplies',
            'is_active' => true
        ]);

        $safetyEquipment = ErpProductGroup::create([
            'name' => 'Safety Equipment',
            'code' => 'SAFE',
            'product_type_id' => $normalTypeId,
            'description' => 'Safety and protective equipment',
            'is_active' => true
        ]);

        $cleaningSupplies = ErpProductGroup::create([
            'name' => 'Cleaning Supplies',
            'code' => 'CLEAN',
            'product_type_id' => $normalTypeId,
            'description' => 'Cleaning and maintenance supplies',
            'is_active' => true
        ]);

        // LIQUIDS - Liquid products sold by volume
        $liquidChemicals = ErpProductGroup::create([
            'name' => 'Liquid Chemicals',
            'code' => 'LIQCHEM',
            'product_type_id' => $liquidTypeId,
            'description' => 'Liquid chemical compounds',
            'is_active' => true
        ]);

        $beverages = ErpProductGroup::create([
            'name' => 'Beverages',
            'code' => 'BEV',
            'product_type_id' => $liquidTypeId,
            'description' => 'Drinks and beverages',
            'is_active' => true
        ]);

        $fuels = ErpProductGroup::create([
            'name' => 'Fuels',
            'code' => 'FUEL',
            'product_type_id' => $liquidTypeId,
            'description' => 'Fuels and lubricants',
            'is_active' => true
        ]);

        // SERVICES - Service offerings
        $consulting = ErpProductGroup::create([
            'name' => 'Consulting',
            'code' => 'CONSULT',
            'product_type_id' => $serviceTypeId,
            'description' => 'Professional consulting services',
            'is_active' => true
        ]);

        $maintenance = ErpProductGroup::create([
            'name' => 'Maintenance',
            'code' => 'MAINT',
            'product_type_id' => $serviceTypeId,
            'description' => 'Maintenance and repair services',
            'is_active' => true
        ]);

        $training = ErpProductGroup::create([
            'name' => 'Training',
            'code' => 'TRAIN',
            'product_type_id' => $serviceTypeId,
            'description' => 'Training and education services',
            'is_active' => true
        ]);

        // Create some deeper nested groups for metals
        $steel = ErpProductGroup::create([
            'name' => 'Steel',
            'code' => 'STEEL',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $metals->id,
            'description' => 'Steel materials and products',
            'is_active' => true
        ]);

        $aluminum = ErpProductGroup::create([
            'name' => 'Aluminum',
            'code' => 'ALUM',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $metals->id,
            'description' => 'Aluminum materials and products',
            'is_active' => true
        ]);

        $copper = ErpProductGroup::create([
            'name' => 'Copper',
            'code' => 'COPP',
            'product_type_id' => $bulkTypeId,
            'parent_id' => $metals->id,
            'description' => 'Copper materials and products',
            'is_active' => true
        ]);

        // Create sub-groups for tools
        $handTools = ErpProductGroup::create([
            'name' => 'Hand Tools',
            'code' => 'HAND',
            'product_type_id' => $normalTypeId,
            'parent_id' => $tools->id,
            'description' => 'Manual tools and instruments',
            'is_active' => true
        ]);

        $powerTools = ErpProductGroup::create([
            'name' => 'Power Tools',
            'code' => 'POWER',
            'product_type_id' => $normalTypeId,
            'parent_id' => $tools->id,
            'description' => 'Electric and pneumatic tools',
            'is_active' => true
        ]);

        $this->command->info('Product groups seeded successfully!');
        $this->command->info('Total groups: ' . ErpProductGroup::count());
    }
}