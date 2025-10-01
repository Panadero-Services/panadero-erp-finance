<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ErpBrand;

class ErpBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'identifier' => 'APL',
                'json' => [
                    'website' => 'https://apple.com',
                    'country' => 'USA',
                    'founded' => 1976,
                    'category' => 'Technology',
                    'color' => '#0066CC',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Premium technology brand',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Samsung',
                'identifier' => 'SMS',
                'json' => [
                    'website' => 'https://samsung.com',
                    'country' => 'South Korea',
                    'founded' => 1969,
                    'category' => 'Electronics',
                    'color' => '#1428A0',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Leading electronics manufacturer',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Microsoft',
                'identifier' => 'MSF',
                'json' => [
                    'website' => 'https://microsoft.com',
                    'country' => 'USA',
                    'founded' => 1975,
                    'category' => 'Software',
                    'color' => '#00BCF2',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Software and cloud services',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Nike',
                'identifier' => 'NKE',
                'json' => [
                    'website' => 'https://nike.com',
                    'country' => 'USA',
                    'founded' => 1964,
                    'category' => 'Sports',
                    'color' => '#E5E5E5',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Athletic footwear and apparel',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Adidas',
                'identifier' => 'ADS',
                'json' => [
                    'website' => 'https://adidas.com',
                    'country' => 'Germany',
                    'founded' => 1949,
                    'category' => 'Sports',
                    'color' => '#EDE734',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Sports and lifestyle brand',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Coca-Cola',
                'identifier' => 'COC',
                'json' => [
                    'website' => 'https://coca-cola.com',
                    'country' => 'USA',
                    'founded' => 1892,
                    'category' => 'Beverages',
                    'color' => '#FF0000',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Beverage and refreshment company',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'PepsiCo',
                'identifier' => 'PEP',
                'json' => [
                    'website' => 'https://pepsico.com',
                    'country' => 'USA',
                    'founded' => 1965,
                    'category' => 'Food & Beverages',
                    'color' => '#004B93',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Food and beverage corporation',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Toyota',
                'identifier' => 'TYT',
                'json' => [
                    'website' => 'https://toyota.com',
                    'country' => 'Japan',
                    'founded' => 1937,
                    'category' => 'Automotive',
                    'color' => '#FF0000',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Automotive manufacturer',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'BMW',
                'identifier' => 'BMW',
                'json' => [
                    'website' => 'https://bmw.com',
                    'country' => 'Germany',
                    'founded' => 1916,
                    'category' => 'Automotive',
                    'color' => '#0066CC',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Luxury automotive brand',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Sony',
                'identifier' => 'SNY',
                'json' => [
                    'website' => 'https://sony.com',
                    'country' => 'Japan',
                    'founded' => 1946,
                    'category' => 'Electronics',
                    'color' => '#000000',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Electronics and entertainment',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Panasonic',
                'identifier' => 'PNS',
                'json' => [
                    'website' => 'https://panasonic.com',
                    'country' => 'Japan',
                    'founded' => 1918,
                    'category' => 'Electronics',
                    'color' => '#0000FF',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Electronics and appliances',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'LG',
                'identifier' => 'LG',
                'json' => [
                    'website' => 'https://lg.com',
                    'country' => 'South Korea',
                    'founded' => 1958,
                    'category' => 'Electronics',
                    'color' => '#A50034',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Electronics and home appliances',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Intel',
                'identifier' => 'INT',
                'json' => [
                    'website' => 'https://intel.com',
                    'country' => 'USA',
                    'founded' => 1968,
                    'category' => 'Semiconductors',
                    'color' => '#0F7DC2',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Semiconductor and processor manufacturer',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'AMD',
                'identifier' => 'AMD',
                'json' => [
                    'website' => 'https://amd.com',
                    'country' => 'USA',
                    'founded' => 1969,
                    'category' => 'Semiconductors',
                    'color' => '#ED1C24',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Semiconductor and processor manufacturer',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'NVIDIA',
                'identifier' => 'NVD',
                'json' => [
                    'website' => 'https://nvidia.com',
                    'country' => 'USA',
                    'founded' => 1993,
                    'category' => 'Graphics',
                    'color' => '#76B900',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Graphics processing and AI',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Dell',
                'identifier' => 'DEL',
                'json' => [
                    'website' => 'https://dell.com',
                    'country' => 'USA',
                    'founded' => 1984,
                    'category' => 'Computers',
                    'color' => '#007DB8',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Computer technology company',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'HP',
                'identifier' => 'HP',
                'json' => [
                    'website' => 'https://hp.com',
                    'country' => 'USA',
                    'founded' => 1939,
                    'category' => 'Computers',
                    'color' => '#0096D6',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Technology and printing solutions',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Lenovo',
                'identifier' => 'LNV',
                'json' => [
                    'website' => 'https://lenovo.com',
                    'country' => 'China',
                    'founded' => 1984,
                    'category' => 'Computers',
                    'color' => '#E2231A',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Computer and mobile device manufacturer',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Asus',
                'identifier' => 'ASU',
                'json' => [
                    'website' => 'https://asus.com',
                    'country' => 'Taiwan',
                    'founded' => 1989,
                    'category' => 'Computers',
                    'color' => '#000000',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Computer hardware and electronics',
                'is_active' => true,
                'is_locked' => false
            ],
            [
                'name' => 'Acer',
                'identifier' => 'ACR',
                'json' => [
                    'website' => 'https://acer.com',
                    'country' => 'Taiwan',
                    'founded' => 1976,
                    'category' => 'Computers',
                    'color' => '#83B81A',
                    'textColor' => '#FFFFFF'
                ],
                'comment' => 'Computer and electronics manufacturer',
                'is_active' => true,
                'is_locked' => false
            ]
        ];

        foreach ($brands as $brand) {
            ErpBrand::create($brand);
        }
    }
}