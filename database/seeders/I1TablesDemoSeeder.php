<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class I1TablesDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insert Languages
        DB::table('i1_languages')->insert([
            ['name' => 'English', 'identifier' => 'en'],
            ['name' => 'Nederlands', 'identifier' => 'nl'],
            ['name' => 'Frans', 'identifier' => 'fr'],
        ]);

        // 2. Insert Access Groups
        DB::table('i1_access_groups')->insert([
            [
                'name' => 'TPT',
                'user_level' => 1,
                'icon_img' => '../image/programmer32.png',
                'description' => 'inserted initially'
            ],
            [
                'name' => 'BAS',
                'user_level' => 2,
                'icon_img' => '../image/userred32.png',
                'description' => 'inserted initially'
            ],
        ]);

        // 3. Insert System Customers
        DB::table('i1_customers')->insert([
            [
                'name' => 'TPT',
                'identifier' => 'TPT',
                'icon_img' => '../image/user1-32.png',
                'address' => 'Finlandweg 21',
                'postal_code' => '4538BL',
                'city' => 'Terneuzen',
                'i1_country_id' => 1
            ],
            [
                'name' => 'basf2',
                'identifier' => 'BAS',
                'icon_img' => '../image/user2-32.png',
                'address' => 'Zandstraat 33',
                'postal_code' => '1144XF',
                'city' => 'Colpontier',
                'i1_country_id' => 2
            ],
        ]);

        // 4. Insert Statuses
        DB::table('i1_statuses')->insert([
            ['name' => 'pending', 'identifier' => 'p'],
            ['name' => 'released', 'identifier' => 'r'],
            ['name' => 'finished', 'identifier' => 'f'],
            ['name' => 'on-hold', 'identifier' => 'o'],
            ['name' => 'history', 'identifier' => 'h'],
        ]);

        // 5. Insert Config Categories
        DB::table('i1_config_categories')->insert([
            ['name' => 'general'],
            ['name' => 'cmr'],
            ['name' => 'indigo_tag'],
            ['name' => 'privs'],
            ['name' => 'widget'],
        ]);

        // 6. Insert Config
        DB::table('i1_config')->insert([
            [
                'key' => 'admin_record_unlock_key',
                'value' => 'v',
                'i1_config_category_id' => 1
            ],
            [
                'key' => 'screenwidth_lib',
                'value' => '1800',
                'i1_config_category_id' => 1
            ],
            [
                'key' => 'screenwidth_basf2',
                'value' => '1800',
                'i1_config_category_id' => 1
            ],
            [
                'key' => 'convert_tablename',
                'value' => "(tablename:=tablename)\n(baseprod:=i1_products)\n(mix1prod:=i1_products)\n(mix2prod:=i1_products)\n(mix3prod:=i1_products)\n(balanceprod:=i1_products)\n(source_site:=i1_sites)\n(dest_site:=i1_sites)\n(storage_from:=i1_storage)\n(storage_to:=i1_storage)",
                'i1_config_category_id' => 1
            ],
        ]);

        // 7. Insert Users
        DB::table('i1_users')->insert([
            [
                'username' => 'fd',
                'full_name' => 'fred dijkstra',
                'password' => Hash::make('freD#2011'),
                'start_page' => 'welcome.php',
                'email' => 'fdijkstra@tpt.nl',
                'reg_date' => now(),
                'access_level' => 2,
                'i1_access_group_id' => 1,
                'i1_customer_id' => 1,
                'details' => 'inserted initially'
            ],
        ]);

        // 8. Insert Countries
        DB::table('i1_countries')->insert([
            ['name' => 'Nederland', 'identifier' => 'nl', 'max_temperature' => 50],
            ['name' => 'Duitsland', 'identifier' => 'du', 'max_temperature' => 40],
            ['name' => 'Belgie', 'identifier' => 'be', 'max_temperature' => 44],
            ['name' => 'Frankrijk', 'identifier' => 'fr', 'max_temperature' => 40],
            ['name' => 'UNITED KINGDOM', 'identifier' => 'uk', 'max_temperature' => 40],
            ['name' => 'UKRAINE', 'identifier' => 'ue', 'max_temperature' => 40],
            ['name' => 'RUSSIA', 'identifier' => 'ru', 'max_temperature' => 40],
            ['name' => 'ROMANIA', 'identifier' => 'rm', 'max_temperature' => 40],
            ['name' => 'GREECE', 'identifier' => 'gr', 'max_temperature' => 40],
            ['name' => 'SWISS', 'identifier' => 'sw', 'max_temperature' => 40],
        ]);

        // 9. Insert Sites
        DB::table('i1_sites')->insert([
            [
                'identifier' => 'TPT',
                'name' => 'TPT BV',
                'address' => 'Finlandweg 21',
                'city' => 'Terneuzen',
                'postal_code' => '4538BL',
                'i1_country_id' => 1,
                'i1_user_id' => 1
            ],
        ]);

        // 10. Insert Product Groups
        DB::table('i1_product_groups')->insert([
            ['identifier' => 'FCC', 'name' => 'Fluid Cracking Catalyst'],
            ['identifier' => 'ECAT', 'name' => 'Equilibrium Catalyst'],
            ['identifier' => 'ADDITIVES', 'name' => 'Additives'],
        ]);

        // 11. Insert Products
        DB::table('i1_products')->insert([
            [
                'identifier' => 'FCC',
                'name' => 'FLUID CRACKING CATALYST',
                'i1_product_group_id' => 1,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'FLUID CRACKING CATALYST',
                'un_fr' => 'FLUID CRACKING CATALYST',
                'un_uk' => 'STATISTIC NR. 3815 9090',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'ECAT',
                'name' => 'EQUILIBRIUM CATALYST',
                'i1_product_group_id' => 2,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'EQUILIBRIUM CATALYST',
                'un_fr' => 'EQUILIBRIUM CATALYST',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP500',
                'name' => 'USP 500 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 500 PROMOTOR',
                'un_fr' => 'USP 500 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP1000',
                'name' => 'USP 1000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 1000 PROMOTOR',
                'un_fr' => 'USP 1000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP2000',
                'name' => 'USP 2000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 2000 PROMOTOR',
                'un_fr' => 'USP 2000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP3000',
                'name' => 'USP 3000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 3000 PROMOTOR',
                'un_fr' => 'USP 3000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP4000',
                'name' => 'USP 4000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 4000 PROMOTOR',
                'un_fr' => 'USP 4000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP5000',
                'name' => 'USP 5000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 5000 PROMOTOR',
                'un_fr' => 'USP 5000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP6000',
                'name' => 'USP 6000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 6000 PROMOTOR',
                'un_fr' => 'USP 6000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP7000',
                'name' => 'USP 7000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 7000 PROMOTOR',
                'un_fr' => 'USP 7000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
            [
                'identifier' => 'USP8000',
                'name' => 'USP 8000 PROMOTOR',
                'i1_product_group_id' => 3,
                'purity' => 100,
                'density' => 0.90,
                'un_code' => 0,
                'un_nl' => 'USP 8000 PROMOTOR',
                'un_fr' => 'USP 8000 PROMOTOR',
                'un_uk' => 'STATISTIC NR. 3815 9090 90',
                'un_de' => 'MANUFACTURING COMPANY: basf2',
                'is_base_product' => true,
                'i1_base_product_id' => 3,
                'i1_balance_product_id' => 1,
                'base_percentage' => 100
            ],
        ]);

        // 12. Insert Forwarders
        DB::table('i1_forwarders')->insert([
            [
                'identifier' => 'TPT',
                'name' => 'TPT TRANSPORT BV',
                'address' => 'Finlandweg 21',
                'city' => 'Terneuzen',
                'postal_code' => '4538BL',
                'i1_country_id' => 1,
                'i1_user_id' => 1
            ],
            [
                'identifier' => 'BASF',
                'name' => 'BASF TRANSPORT',
                'address' => 'Zandstraat 33',
                'city' => 'Colpontier',
                'postal_code' => '1144XF',
                'i1_country_id' => 2,
                'i1_user_id' => 1
            ],
            [
                'identifier' => 'ESSO',
                'name' => 'ESSO TRANSPORT',
                'address' => 'Havenweg 15',
                'city' => 'Rotterdam',
                'postal_code' => '3000AA',
                'i1_country_id' => 1,
                'i1_user_id' => 1
            ],
            [
                'identifier' => 'SHELL',
                'name' => 'SHELL TRANSPORT',
                'address' => 'Shellweg 1',
                'city' => 'Amsterdam',
                'postal_code' => '1000AB',
                'i1_country_id' => 1,
                'i1_user_id' => 1
            ],
            [
                'identifier' => 'BP',
                'name' => 'BP TRANSPORT',
                'address' => 'BP Street 10',
                'city' => 'London',
                'postal_code' => 'SW1A 1AA',
                'i1_country_id' => 5,
                'i1_user_id' => 1
            ],
        ]);

        // 13. Insert Business Customers
        DB::table('i1_business_customers')->insert([
            [
                'identifier' => 'ESSJ',
                'name' => 'ESSO RAFFINAGE SAS - RAF DE PORT JEROME',
                'address' => '1 AVENUE KENNEDY',
                'city' => 'NOTRE DAME DE GRAVENCHON',
                'postal_code' => '76330',
                'i1_country_id' => 4,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'ESUK',
                'name' => 'ESSO PETROLEUM COMPANY LTD',
                'address' => 'GATE NO. 1 - FAWLEY REFINERY',
                'city' => 'SOUTHAMPTON S4045 1DS',
                'postal_code' => 'SURRREY KT22 8UX',
                'i1_country_id' => 5,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'EAUG',
                'name' => 'ESSO RAFFINERIA DI AUGUSTA',
                'address' => '',
                'city' => 'SICILY',
                'postal_code' => '96011',
                'i1_country_id' => 5,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'KREM',
                'name' => 'JSC UKRTATNAFTA',
                'address' => 'SWISHTOVSKAYA 3',
                'city' => 'KREMENCHUG',
                'postal_code' => '39609',
                'i1_country_id' => 6,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'ESSA',
                'name' => 'ESSO BELGIUM, DIV.OF EXXON MOBIL PETR & CHEM, BVBA',
                'address' => 'HAVEN 447, POLDERDIJKWEG 3',
                'city' => 'ANTWERPEN',
                'postal_code' => '2030',
                'i1_country_id' => 3,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'RUHR',
                'name' => 'RUHR OEL GMBH FCC UNIT',
                'address' => 'JOHANNASTRASSE 2-8',
                'city' => 'GELSENKIRCHEN-HASSEL',
                'postal_code' => '45899',
                'i1_country_id' => 2,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'MOSC',
                'name' => 'MOSCOW OIL REFINERY, FCC UNIT',
                'address' => 'KAPOTNIA, 2 KVARTAL',
                'city' => 'MOSCOW',
                'postal_code' => '109429',
                'i1_country_id' => 7,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'EING',
                'name' => 'PETROPLUS RAFFINERIE, INGOLSTADT GMBH',
                'address' => 'ESSOSTRASSE 1',
                'city' => 'KOESCHING',
                'postal_code' => '85092',
                'i1_country_id' => 2,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'PETRO',
                'name' => 'JSC PETROTEL-LUKOIL, PLOESTI',
                'address' => 'MIBAU BRAVU STR. 236, PRAHOVA COUNTRY',
                'city' => '',
                'postal_code' => '',
                'i1_country_id' => 8,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'BPRO',
                'name' => 'BP RAFFINADERIJ ROTTERDAM BV',
                'address' => 'D`ARCYWEG 76 HAVENNR 6425',
                'city' => 'ROTTERDAM',
                'postal_code' => '3198NA',
                'i1_country_id' => 1,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'MOH',
                'name' => 'MOTOR OIL (HELLAS) CORINTH',
                'address' => 'REFINERIES SA , AG, THEODORI',
                'city' => 'CORINTH',
                'postal_code' => '20100',
                'i1_country_id' => 9,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'SHPC',
                'name' => 'STE COURONNAISE DE RAFFINAGE, RAFFINERIE PETROPLUS',
                'address' => 'PR. CHI/LAB B&S-PTCO 17',
                'city' => 'PETIT COURONNE',
                'postal_code' => '76650',
                'i1_country_id' => 4,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
            [
                'identifier' => 'SHPE',
                'name' => 'SHELL PERNISS',
                'address' => 'STRAATNAAM',
                'city' => 'HOOGVLIET',
                'postal_code' => '1234AA',
                'i1_country_id' => 1,
                'i1_forwarder_id' => 1,
                'i1_product_id' => 1,
                'i1_user_id' => 1,
                'default_qty' => 30
            ],
        ]);

        // 14. Insert Storage
        DB::table('i1_storage')->insert([
            [
                'identifier' => 'SS1000',
                'i1_source_site_id' => 1,
                'i1_product_id' => 1,
                'max_qty' => 1000,
                'actual_qty' => 500,
                'tag' => 'SS1000',
                'units' => 'Ton',
                'actual_density' => 1.1,
                'actual_purity' => 100,
                'default_sequence' => 1
            ],
            [
                'identifier' => 'SS1500',
                'i1_source_site_id' => 1,
                'i1_product_id' => 2,
                'max_qty' => 1500,
                'actual_qty' => 750,
                'tag' => 'SS1500',
                'units' => 'Ton',
                'actual_density' => 1.1,
                'actual_purity' => 100,
                'default_sequence' => 2
            ],
            [
                'identifier' => 'SS2000',
                'i1_source_site_id' => 1,
                'i1_product_id' => 3,
                'max_qty' => 2000,
                'actual_qty' => 1000,
                'tag' => 'SS2000',
                'units' => 'Ton',
                'actual_density' => 1.1,
                'actual_purity' => 100,
                'default_sequence' => 3
            ],
            [
                'identifier' => 'SS2200',
                'i1_source_site_id' => 1,
                'i1_product_id' => 4,
                'max_qty' => 2200,
                'actual_qty' => 1100,
                'tag' => 'SS2200',
                'units' => 'Ton',
                'actual_density' => 1.1,
                'actual_purity' => 100,
                'default_sequence' => 4
            ],
        ]);

        // 15. Insert Sample Orders
        DB::table('i1_orders')->insert([
            [
                'tpt_order_nr' => 'TPT-in-2024-001',
                'cust_order_nr' => 'ESSJ-2024-001',
                'i1_customer_id' => 1,
                'i1_forwarder_id' => 1,
                'i1_source_site_id' => 1,
                'i1_dest_site_id' => 1,
                'i1_product_id' => 1,
                'order_qty' => 30.0,
                'i1_status_id' => 1,
                'r_date' => now(),
                'e_date' => now()->addDays(7),
                'docs_array' => '(CMR)',
                'analyse_array' => '',
                'comment' => 'Sample incoming order'
            ],
            [
                'tpt_order_nr' => 'TPT-out-2024-001',
                'cust_order_nr' => 'BPRO-2024-001',
                'i1_customer_id' => 10,
                'i1_forwarder_id' => 1,
                'i1_source_site_id' => 1,
                'i1_dest_site_id' => 1,
                'i1_product_id' => 1,
                'order_qty' => 25.0,
                'i1_status_id' => 2,
                'r_date' => now()->subDays(5),
                'e_date' => now()->addDays(2),
                'docs_array' => '(CMR)',
                'analyse_array' => '',
                'comment' => 'Sample outgoing order'
            ],
        ]);
    }
}