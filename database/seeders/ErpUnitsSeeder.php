<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ErpUnit;

class ErpUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Weight units
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_decimal' => true],
            ['name' => 'Gram', 'symbol' => 'g', 'is_decimal' => true],
            ['name' => 'Pound', 'symbol' => 'lb', 'is_decimal' => true],
            ['name' => 'Ounce', 'symbol' => 'oz', 'is_decimal' => true],
            ['name' => 'Ton', 'symbol' => 't', 'is_decimal' => true],
            
            // Volume units
            ['name' => 'Liter', 'symbol' => 'L', 'is_decimal' => true],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'is_decimal' => true],
            ['name' => 'Gallon', 'symbol' => 'gal', 'is_decimal' => true],
            ['name' => 'Cubic Meter', 'symbol' => 'm³', 'is_decimal' => true],
            ['name' => 'Cubic Centimeter', 'symbol' => 'cm³', 'is_decimal' => true],
            
            // Length units
            ['name' => 'Meter', 'symbol' => 'm', 'is_decimal' => true],
            ['name' => 'Centimeter', 'symbol' => 'cm', 'is_decimal' => true],
            ['name' => 'Millimeter', 'symbol' => 'mm', 'is_decimal' => true],
            ['name' => 'Inch', 'symbol' => 'in', 'is_decimal' => true],
            ['name' => 'Foot', 'symbol' => 'ft', 'is_decimal' => true],
            
            // Count units
            ['name' => 'Piece', 'symbol' => 'pcs', 'is_decimal' => false],
            ['name' => 'Each', 'symbol' => 'ea', 'is_decimal' => false],
            ['name' => 'Unit', 'symbol' => 'unit', 'is_decimal' => false],
            ['name' => 'Box', 'symbol' => 'box', 'is_decimal' => false],
            ['name' => 'Pack', 'symbol' => 'pack', 'is_decimal' => false],
            ['name' => 'Set', 'symbol' => 'set', 'is_decimal' => false],
            ['name' => 'Pair', 'symbol' => 'pair', 'is_decimal' => false],
            ['name' => 'Dozen', 'symbol' => 'doz', 'is_decimal' => false],
            ['name' => 'Hundred', 'symbol' => 'hundred', 'is_decimal' => false],
            ['name' => 'Thousand', 'symbol' => 'k', 'is_decimal' => false],
            
            // Area units
            ['name' => 'Square Meter', 'symbol' => 'm²', 'is_decimal' => true],
            ['name' => 'Square Foot', 'symbol' => 'ft²', 'is_decimal' => true],
            ['name' => 'Square Inch', 'symbol' => 'in²', 'is_decimal' => true],
            
            // Time units
            ['name' => 'Hour', 'symbol' => 'hr', 'is_decimal' => true],
            ['name' => 'Minute', 'symbol' => 'min', 'is_decimal' => true],
            ['name' => 'Second', 'symbol' => 'sec', 'is_decimal' => true],
            ['name' => 'Day', 'symbol' => 'day', 'is_decimal' => false],
            ['name' => 'Week', 'symbol' => 'week', 'is_decimal' => false],
            ['name' => 'Month', 'symbol' => 'month', 'is_decimal' => false],
            ['name' => 'Year', 'symbol' => 'year', 'is_decimal' => false],
        ];

        foreach ($units as $unit) {
            ErpUnit::create($unit);
        }

        $this->command->info('Units seeded successfully!');
        $this->command->info('Total units: ' . ErpUnit::count());
    }
}