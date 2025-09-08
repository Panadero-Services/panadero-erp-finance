<?php

namespace Panadero\Erp\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vacancies = [
            [
                'title' => 'Senior Frontend Developer',
                'description' => 'We are looking for an experienced Frontend Developer to join our engineering team. You will be responsible for building user-facing features and ensuring the best user experience.',
                'requirements' => 'Bachelor\'s degree in Computer Science or related field, 5+ years of frontend development experience, proficiency in Vue.js, React, or Angular, experience with modern CSS frameworks, strong understanding of responsive design principles.',
                'responsibilities' => 'Develop and maintain user-facing features, collaborate with design team to implement UI/UX designs, optimize applications for maximum speed and scalability, ensure cross-browser compatibility, participate in code reviews and technical discussions.',
                'department_id' => 2, // Engineering
                'position_type' => 'Senior Software Engineer',
                'employment_type' => 'full_time',
                'experience_level' => 'senior',
                'location' => 'San Francisco, CA',
                'remote_allowed' => true,
                'salary_min' => 90000.00,
                'salary_max' => 130000.00,
                'currency' => 'USD',
                'required_skills' => json_encode(['Vue.js', 'JavaScript', 'HTML5', 'CSS3', 'Git', 'REST APIs']),
                'preferred_skills' => json_encode(['TypeScript', 'Webpack', 'Jest', 'Docker', 'AWS']),
                'benefits' => json_encode(['Health Insurance', 'Dental Insurance', 'Vision Insurance', '401k Matching', 'Flexible PTO', 'Remote Work']),
                'application_deadline' => '2025-12-31',
                'start_date' => '2025-10-01',
                'status' => 'published',
                'created_by' => 1,
                'max_applications' => 100,
                'current_applications' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Marketing Specialist',
                'description' => 'Join our marketing team as a Marketing Specialist to help drive brand awareness and customer acquisition through various digital marketing channels.',
                'requirements' => 'Bachelor\'s degree in Marketing or related field, 3+ years of digital marketing experience, proficiency in Google Analytics, Google Ads, and social media platforms, strong analytical skills, excellent written and verbal communication skills.',
                'responsibilities' => 'Develop and execute digital marketing campaigns, manage social media presence, analyze campaign performance and optimize for better results, collaborate with content team to create engaging materials, track and report on key marketing metrics.',
                'department_id' => 3, // Marketing
                'position_type' => 'Marketing Specialist',
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location' => 'New York, NY',
                'remote_allowed' => true,
                'salary_min' => 55000.00,
                'salary_max' => 75000.00,
                'currency' => 'USD',
                'required_skills' => json_encode(['Digital Marketing', 'Google Analytics', 'Social Media Marketing', 'Content Creation', 'Email Marketing']),
                'preferred_skills' => json_encode(['HubSpot', 'Mailchimp', 'Adobe Creative Suite', 'SEO', 'PPC']),
                'benefits' => json_encode(['Health Insurance', 'Dental Insurance', '401k Matching', 'Professional Development', 'Flexible Schedule']),
                'application_deadline' => '2025-11-30',
                'start_date' => '2025-09-15',
                'status' => 'published',
                'created_by' => 1,
                'max_applications' => 75,
                'current_applications' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'HR Coordinator',
                'description' => 'We are seeking an HR Coordinator to support our human resources department with administrative tasks, recruitment coordination, and employee onboarding.',
                'requirements' => 'Bachelor\'s degree in Human Resources or related field, 2+ years of HR experience, strong organizational skills, excellent communication skills, proficiency in HRIS systems, knowledge of employment laws and regulations.',
                'responsibilities' => 'Coordinate recruitment processes and interviews, assist with employee onboarding and orientation, maintain employee records and documentation, support HR projects and initiatives, assist with benefits administration, handle employee inquiries.',
                'department_id' => 1, // HR
                'position_type' => 'HR Coordinator',
                'employment_type' => 'full_time',
                'experience_level' => 'entry',
                'location' => 'Chicago, IL',
                'remote_allowed' => false,
                'salary_min' => 45000.00,
                'salary_max' => 60000.00,
                'currency' => 'USD',
                'required_skills' => json_encode(['HR Administration', 'Recruitment', 'Employee Relations', 'Microsoft Office', 'HRIS']),
                'preferred_skills' => json_encode(['BambooHR', 'Workday', 'PHR Certification', 'Project Management']),
                'benefits' => json_encode(['Health Insurance', 'Dental Insurance', 'Vision Insurance', '401k', 'Paid Time Off', 'Professional Development']),
                'application_deadline' => '2025-10-15',
                'start_date' => '2025-09-01',
                'status' => 'published',
                'created_by' => 1,
                'max_applications' => 50,
                'current_applications' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sales Representative',
                'description' => 'Join our sales team as a Sales Representative to help drive revenue growth and build strong customer relationships in our target markets.',
                'requirements' => 'Bachelor\'s degree in Business or related field, 2+ years of sales experience, strong communication and negotiation skills, self-motivated and results-driven, proficiency in CRM systems, ability to work independently and as part of a team.',
                'responsibilities' => 'Identify and pursue new business opportunities, build and maintain relationships with clients, meet and exceed sales targets, prepare and deliver sales presentations, collaborate with marketing team on lead generation, maintain accurate sales records.',
                'department_id' => 4, // Sales
                'position_type' => 'Sales Representative',
                'employment_type' => 'full_time',
                'experience_level' => 'mid',
                'location' => 'Austin, TX',
                'remote_allowed' => true,
                'salary_min' => 50000.00,
                'salary_max' => 80000.00,
                'currency' => 'USD',
                'required_skills' => json_encode(['Sales', 'CRM', 'Lead Generation', 'Customer Relations', 'Negotiation']),
                'preferred_skills' => json_encode(['Salesforce', 'HubSpot', 'B2B Sales', 'Account Management']),
                'benefits' => json_encode(['Health Insurance', 'Dental Insurance', '401k', 'Commission', 'Car Allowance', 'Flexible Schedule']),
                'application_deadline' => '2025-12-15',
                'start_date' => '2025-10-15',
                'status' => 'published',
                'created_by' => 1,
                'max_applications' => 80,
                'current_applications' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hr_vacancies')->insert($vacancies);
    }
}
