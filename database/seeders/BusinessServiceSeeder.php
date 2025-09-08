<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BusinessService;

class BusinessServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'item' => 'Home',
                'title' => 'i3-Framework',
                'description' => 'A robust framework designed to streamline and enhance business operations through integrated services.',
                'color' => 'indigo',
                'version' => 'v1.1.08',
                'icon' => 'I3FrameworkIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
                    ['name' => 'Administration', 'url' => '/home/administration', 'route' => 'home.administration'],
                    ['name' => 'Welcome', 'url' => '/home/welkom', 'route' => 'home.welkom'],
                    ['name' => 'Tiers', 'url' => '/home/tiers', 'route' => 'home.tiers'],
                    ['name' => 'Futures', 'url' => '/home/futures', 'route' => 'home.futures'],
                    ['name' => 'Landing', 'url' => '/home/landing', 'route' => 'home.landing'],
                    ['name' => 'BusinessServices', 'url' => '/home/business_services', 'route' => 'home.business_services'],
                    ['name' => 'SolarSys', 'url' => '/home/solarsys', 'route' => 'home.solarsys'],
                    ['name' => 'Sandbox', 'url' => '/home/sandbox', 'route' => 'home.sandbox']
                ],
                'status' => 'ready',
                'progress' => 96,
                'path' => '/'
            ],
            [
                'item' => 'Admin',
                'title' => 'i3-Admin',
                'description' => 'Administration module for i3-Framework.',
                'color' => 'indigo',
                'version' => 'v1.0.01',
                'icon' => 'I3FrameworkIcon',
                'options' => [
                    ['name' => 'Permissions', 'url' => '/admin/permissions', 'route' => 'admin/permissions'],
                    ['name' => 'Roles', 'url' => '/admin/roles', 'route' => 'admin/roles'],
                    ['name' => 'Middleware', 'url' => '/admin/middleware', 'route' => 'admin/middleware']
                ],
                'status' => 'ready',
                'progress' => 30,
                'path' => '/'
            ],
            [
                'item' => 'CMS',
                'title' => 'Content Management',
                'description' => 'Efficiently manage and organize your content, from articles to posts, with an intuitive interface.',
                'color' => 'green',
                'version' => 'v0.7.01',
                'icon' => 'ContentManagementIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/content/posts', 'route' => 'posts'],
                    ['name' => 'Posts', 'url' => '/content/posts', 'route' => 'posts'],
                    ['name' => 'Bento', 'url' => '/bento', 'route' => 'bento']
                ],
                'status' => 'in_progress',
                'progress' => 29,
                'path' => '/'
            ],
            [
                'item' => 'ERP',
                'title' => 'Resource Planning',
                'description' => 'Optimize resource allocation and management to enhance productivity and efficiency.',
                'color' => 'red',
                'version' => 'v3.1.65',
                'icon' => 'ResourcePlanningIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/erp/dashboard', 'route' => 'erp/dashboard'],
                    ['name' => 'Resources', 'url' => '/erp/resources', 'route' => 'erp/resources'],
                    ['name' => 'Mood', 'url' => '/erp/mood', 'route' => 'erp/mood'],
                    ['name' => 'Sand', 'url' => '/erp/sand', 'route' => 'erp/sand']
                ],
                'status' => 'ready',
                'progress' => 60,
                'path' => '/'
            ],
            [
                'item' => 'I3L',
                'title' => 'Logistic Management',
                'description' => 'Streamline your logistics processes to ensure smooth and efficient operations.',
                'color' => 'blue',
                'version' => 'v1.1.04',
                'icon' => 'LogisticsIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Resources', 'url' => '/erp/resources', 'route' => 'erp/resources']
                ],
                'status' => 'in_progress',
                'progress' => 10,
                'path' => '/'
            ],
            [
                'item' => 'I3P',
                'title' => 'Project Management',
                'description' => 'Manage your projects effectively with tools designed to track progress and allocate resources.',
                'color' => 'pink',
                'version' => 'v1.1.05',
                'icon' => 'ProjectIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Projects', 'url' => '/project/projects', 'route' => 'project/projects'],
                    ['name' => 'Plan', 'url' => '/project/plan', 'route' => 'project/plan'],
                    ['name' => 'Work', 'url' => '/project/work', 'route' => 'project/work'],
                    ['name' => 'Budget', 'url' => '/project/budget', 'route' => 'project/budget']
                ],
                'status' => 'featured',
                'progress' => 70,
                'path' => '/'
            ],
            [
                'item' => 'design',
                'title' => 'Design Tool',
                'description' => 'Create and manage diagrams for your business ideas and projects with ease.',
                'color' => 'violet',
                'version' => 'v1.1.05',
                'icon' => 'DesignIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Mind', 'url' => '/design/mind', 'route' => 'design/mind'],
                    ['name' => 'Pert', 'url' => '/design/pert', 'route' => 'design/pert'],
                    ['name' => 'Lane', 'url' => '/design/lane', 'route' => 'design/lane']
                ],
                'status' => 'experimental',
                'progress' => 80,
                'path' => '/'
            ],
            [
                'item' => 'Sales',
                'title' => 'Ecommerce',
                'description' => 'Control and manage your e-commerce business with integrated tools and analytics.',
                'color' => 'purple',
                'version' => 'v0.1.04',
                'icon' => 'EcommerceIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/ecommerce/dashboard', 'route' => 'ecommerce/dashboard'],
                    ['name' => 'Storefront', 'url' => '/ecommerce/storefront', 'route' => 'ecommerce/storefront']
                ],
                'status' => 'experimental',
                'progress' => 5,
                'path' => '/'
            ],
            [
                'item' => 'Web3',
                'title' => 'Web3 Innovations',
                'description' => 'Leverage decentralized applications to enhance your business operations.',
                'color' => 'teal',
                'version' => 'v1.0.00',
                'icon' => 'Web3Icon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/erp/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Web3', 'url' => '/web3', 'route' => 'web3']
                ],
                'status' => 'pending',
                'progress' => 40,
                'path' => '/'
            ],
            [
                'item' => 'AI',
                'title' => 'AI Agents',
                'description' => 'Utilize AI agents to automate and enhance your business processes.',
                'color' => 'purple',
                'version' => 'v1.0.02',
                'icon' => 'AiIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Contracts', 'url' => '/web3', 'route' => 'web3'],
                    ['name' => 'Bots', 'url' => '/ai/bots', 'route' => 'ai/bots'],
                    ['name' => 'Providers', 'url' => '/ai/providers', 'route' => 'ai/providers'],
                    ['name' => 'Processors', 'url' => '/ai/processors', 'route' => 'ai/processors'],
                    ['name' => 'Executors', 'url' => '/ai/executors', 'route' => 'ai/executors']
                ],
                'status' => 'testing',
                'progress' => 12,
                'path' => '/'
            ],
            [
                'item' => 'SMedia',
                'title' => 'Social Media',
                'description' => 'Manage your social media interactions and enhance your online presence.',
                'color' => 'yellow',
                'version' => 'v1.0.00',
                'icon' => 'SocialMediaIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Resources', 'url' => '/erp/resources', 'route' => 'erp/resources']
                ],
                'status' => 'pending',
                'progress' => 10,
                'path' => '/'
            ],
            [
                'item' => 'Bots',
                'title' => 'Automated Scripts',
                'description' => 'This function takes care of your decentralized payment architecture.',
                'color' => 'indigo',
                'version' => 'v1.0.00',
                'icon' => 'WalletIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Resources', 'url' => '/erp/resources', 'route' => 'erp/resources']
                ],
                'status' => 'testing',
                'progress' => 20,
                'path' => '/'
            ],
            [
                'item' => 'I1',
                'title' => 'Indigo1 Legacy Mode',
                'description' => 'Handle backward compatible Indigo1 business processes with ease.',
                'color' => 'indigo',
                'version' => 'v1.0.00',
                'icon' => 'TruckIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Orders', 'url' => '/indigo1/i1_orders', 'route' => 'indigo1.i1_orders'],
                    ['name' => 'Forwarders', 'url' => '/indigo1/i1_forwarders', 'route' => 'indigo1.i1_forwarders'],
                    ['name' => 'Customers', 'url' => '/indigo1/i1_business_customers', 'route' => 'indigo1.i1_business_customers']
                ],
                'status' => 'deprecated',
                'progress' => 10,
                'path' => '/'
            ],
            [
                'item' => 'I2',
                'title' => 'Indigo2 Legacy Mode',
                'description' => 'Manage backward compatible Indigo2 business processes efficiently.',
                'color' => 'indigo',
                'version' => 'v1.0.00',
                'icon' => 'PuzzlePieceIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Resources', 'url' => '/erp/resources', 'route' => 'erp/resources']
                ],
                'status' => 'deprecated',
                'progress' => 10,
                'path' => '/'
            ],
            [
                'item' => 'Projects',
                'title' => 'Projects List',
                'description' => 'Table holding all projects',
                'color' => 'blue',
                'version' => 'v1.0.00',
                'icon' => 'PuzzlePieceIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'Projects', 'url' => '/shared/projects', 'route' => 'shared/projects']

                ],
                'status' => 'ready',
                'progress' => 90,
                'path' => '/shared/'
            ],
            [
                'item' => 'Services',
                'title' => 'Services List',
                'description' => 'Table holding all Services',
                'color' => 'blue',
                'version' => 'v1.0.00',
                'icon' => 'PuzzlePieceIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'BusinessServicesShared', 'url' => '/shared/business_services', 'route' => 'shared.business_services'],
                    ['name' => 'BusinessServices', 'url' => '/home/business_services', 'route' => 'home.business_services']

                ],
                'status' => 'ready',
                'progress' => 90,
                'path' => '/shared/'
            ],
            [
                'item' => 'Vendors',
                'title' => 'Vendors List',
                'description' => 'Table holding all Vendors',
                'color' => 'blue',
                'version' => 'v1.0.00',
                'icon' => 'PuzzlePieceIcon',
                'options' => [
                    ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
                    ['name' => 'BusinessServicesShared', 'url' => '/shared/business_services', 'route' => 'shared.business_services'],
                    ['name' => 'VendorsShared', 'url' => '/shared/vendors', 'route' => 'shared.vendors'],
                    ['name' => 'BusinessServices', 'url' => '/home/business_services', 'route' => 'home.business_services']

                ],
                'status' => 'ready',
                'progress' => 90,
                'path' => '/shared/'
            ],


// ERP FUNCTIONS TOP 10
    [
        'item' => 'Finance',
        'title' => 'ERP Finance',
        'description' => 'Comprehensive financial management system with accounting, budgeting, and reporting',
        'color' => 'teal',
        'version' => 'v1.0.00',
        'icon' => 'EcommerceIcon',
        'options' => [
            ['name' => 'Finance', 'url' => '/erp/finance', 'route' => 'erp.finance'],
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home/dashboard'],
            ['name' => 'General Ledger', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'general-ledger'],
            ['name' => 'Accounts Payable', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'accounts-payable'],
            ['name' => 'Accounts Receivable', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'accounts-receivable'],
            ['name' => 'Cash Flow', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'cash-flow'],
            ['name' => 'Budgeting', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'budgeting'],
            ['name' => 'Reporting', 'url' => '/erp/finance', 'route' => 'erp.finance', 'tab' => 'reporting']
        ],
        'status' => 'ready',
        'progress' => 50,
        'path' => '/erp/'
    ],
    [
        'item' => 'Inventry',
        'title' => 'ERP Inventory',
        'description' => 'Complete inventory management with stock tracking, warehousing, and supply chain',
        'color' => 'blue',
        'version' => 'v1.0.00',
        'icon' => 'PackageIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Stock Management', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'stock'],
            ['name' => 'Warehouse Management', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'warehouse'],
            ['name' => 'Purchase Orders', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'purchase-orders'],
            ['name' => 'Supplier Management', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'suppliers'],
            ['name' => 'Inventory Reports', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'reports'],
            ['name' => 'Agent Portal', 'url' => '/erp/inventory', 'route' => 'erp.inventory', 'tab' => 'agent-portal']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Sales',
        'title' => 'ERP Sales',
        'description' => 'Sales management system with CRM, order processing, and customer analytics',
        'color' => 'green',
        'version' => 'v1.0.00',
        'icon' => 'ShoppingCartIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Customer Management', 'url' => '/erp/sales', 'route' => 'erp.sales', 'tab' => 'customers'],
            ['name' => 'Sales Orders', 'url' => '/erp/sales', 'route' => 'erp.sales', 'tab' => 'orders'],
            ['name' => 'Quotations', 'url' => '/erp/sales', 'route' => 'erp.sales', 'tab' => 'quotations'],
            ['name' => 'Sales Analytics', 'url' => '/erp/sales', 'route' => 'erp.sales', 'tab' => 'analytics'],
            ['name' => 'Commission Tracking', 'url' => '/erp/sales', 'route' => 'erp.sales', 'tab' => 'commissions']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'HR',
        'title' => 'ERP HR',
        'description' => 'Human resources management with employee records, payroll, and performance tracking',
        'color' => 'purple',
        'version' => 'v1.0.00',
        'icon' => 'UsersIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Employee Records', 'url' => '/erp/hr', 'route' => 'erp.hr', 'tab' => 'employees'],
            ['name' => 'Payroll Management', 'url' => '/erp/hr', 'route' => 'erp.hr', 'tab' => 'payroll'],
            ['name' => 'Time & Attendance', 'url' => '/erp/hr', 'route' => 'erp.hr', 'tab' => 'attendance'],
            ['name' => 'Performance Reviews', 'url' => '/erp/hr', 'route' => 'erp.hr', 'tab' => 'performance'],
            ['name' => 'Training & Development', 'url' => '/erp/hr', 'route' => 'erp.hr', 'tab' => 'training']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Manufact',
        'title' => 'ERP Manufacturing',
        'description' => 'Production planning, quality control, and manufacturing execution system',
        'color' => 'blue',
        'version' => 'v1.0.00',
        'icon' => 'CogIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Production Planning', 'url' => '/erp/manufacturing', 'route' => 'erp.manufacturing', 'tab' => 'planning'],
            ['name' => 'Work Orders', 'url' => '/erp/manufacturing', 'route' => 'erp.manufacturing', 'tab' => 'work-orders'],
            ['name' => 'Quality Control', 'url' => '/erp/manufacturing', 'route' => 'erp.manufacturing', 'tab' => 'quality'],
            ['name' => 'Equipment Maintenance', 'url' => '/erp/manufacturing', 'route' => 'erp.manufacturing', 'tab' => 'maintenance'],
            ['name' => 'Production Reports', 'url' => '/erp/manufacturing', 'route' => 'erp.manufacturing', 'tab' => 'reports']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Procure',
        'title' => 'ERP Procurement',
        'description' => 'Strategic sourcing, vendor management, and procurement automation',
        'color' => 'indigo',
        'version' => 'v1.0.00',
        'icon' => 'ClipboardDocumentListIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Vendor Management', 'url' => '/erp/procurement', 'route' => 'erp.procurement', 'tab' => 'vendors'],
            ['name' => 'Purchase Requisitions', 'url' => '/erp/procurement', 'route' => 'erp.procurement', 'tab' => 'requisitions'],
            ['name' => 'Contract Management', 'url' => '/erp/procurement', 'route' => 'erp.procurement', 'tab' => 'contracts'],
            ['name' => 'Supplier Evaluation', 'url' => '/erp/procurement', 'route' => 'erp.procurement', 'tab' => 'evaluation'],
            ['name' => 'Cost Analysis', 'url' => '/erp/procurement', 'route' => 'erp.procurement', 'tab' => 'cost-analysis']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Projects',
        'title' => 'ERP Projects',
        'description' => 'Project planning, resource allocation, and project financial management',
        'color' => 'pink',
        'version' => 'v1.0.00',
        'icon' => 'FolderIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Project Planning', 'url' => '/erp/projects', 'route' => 'erp.projects', 'tab' => 'planning'],
            ['name' => 'Resource Allocation', 'url' => '/erp/projects', 'route' => 'erp.projects', 'tab' => 'resources'],
            ['name' => 'Task Management', 'url' => '/erp/projects', 'route' => 'erp.projects', 'tab' => 'tasks'],
            ['name' => 'Project Financials', 'url' => '/erp/projects', 'route' => 'erp.projects', 'tab' => 'financials'],
            ['name' => 'Project Reports', 'url' => '/erp/projects', 'route' => 'erp.projects', 'tab' => 'reports']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Customer',
        'title' => 'ERP Customer Service',
        'description' => 'Customer support, ticket management, and service level monitoring',
        'color' => 'yellow',
        'version' => 'v1.0.00',
        'icon' => 'QuestionMarkCircleIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Ticket Management', 'url' => '/erp/customer-service', 'route' => 'erp.customer-service', 'tab' => 'tickets'],
            ['name' => 'Customer Support', 'url' => '/erp/customer-service', 'route' => 'erp.customer-service', 'tab' => 'support'],
            ['name' => 'Knowledge Base', 'url' => '/erp/customer-service', 'route' => 'erp.customer-service', 'tab' => 'knowledge'],
            ['name' => 'Service Level Monitoring', 'url' => '/erp/customer-service', 'route' => 'erp.customer-service', 'tab' => 'sla'],
            ['name' => 'Customer Analytics', 'url' => '/erp/customer-service', 'route' => 'erp.customer-service', 'tab' => 'analytics']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Analytics',
        'title' => 'ERP Analytics',
        'description' => 'Business intelligence, reporting, and data visualization across all modules',
        'color' => 'red',
        'version' => 'v1.0.00',
        'icon' => 'ChartBarIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Business Intelligence', 'url' => '/erp/analytics', 'route' => 'erp.analytics', 'tab' => 'bi'],
            ['name' => 'Financial Reports', 'url' => '/erp/analytics', 'route' => 'erp.analytics', 'tab' => 'financial-reports'],
            ['name' => 'Operational Reports', 'url' => '/erp/analytics', 'route' => 'erp.analytics', 'tab' => 'operational'],
            ['name' => 'Data Visualization', 'url' => '/erp/analytics', 'route' => 'erp.analytics', 'tab' => 'visualization'],
            ['name' => 'Custom Dashboards', 'url' => '/erp/analytics', 'route' => 'erp.analytics', 'tab' => 'dashboards']
        ],
        'status' => 'ready',
        'progress' => 5,
        'path' => '/erp/'
    ],
    [
        'item' => 'Compliance',
        'title' => 'ERP Compliance',
        'description' => 'Regulatory compliance, audit trails, and governance management with AI-powered analysis',
                'color' => 'teal',
        'version' => 'v1.0.00',
        'icon' => 'ShieldCheckIcon',
        'options' => [
            ['name' => 'Dashboard', 'url' => '/home/dashboard', 'route' => 'home.dashboard'],
            ['name' => 'Regulatory Compliance', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'regulatory'],
            ['name' => 'Audit Trails', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'audit'],
            ['name' => 'Risk Management', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'risk'],
            ['name' => 'Policy Management', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'policies'],
            ['name' => 'Compliance Reports', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'reports'],
            ['name' => 'Root Cause Analysis', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'rca'],
            ['name' => 'Agent Portal', 'url' => '/erp/compliance', 'route' => 'erp.compliance', 'tab' => 'agent-portal']
        ],
        'status' => 'ready',
        'progress' => 100,
        'path' => '/erp/'
    ]

    ];

        foreach ($services as $service) {
            BusinessService::updateOrCreate(
                ['item' => $service['item']],
                array_merge($service, [
                    'links' => [],
                    'is_active' => true
                ])
            );
        }
    }
}; 