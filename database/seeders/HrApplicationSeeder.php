<?php

namespace Panadero\Erp\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'vacancy_id' => 1, // Senior Frontend Developer
                'first_name' => 'Alex',
                'last_name' => 'Thompson',
                'email' => 'alex.thompson@email.com',
                'phone' => '+1-555-1001',
                'cover_letter' => 'I am excited to apply for the Senior Frontend Developer position. With over 6 years of experience in frontend development, I have a strong background in Vue.js, React, and modern web technologies. I am passionate about creating user-friendly interfaces and have a proven track record of delivering high-quality projects on time.',
                'resume_path' => '/storage/resumes/alex_thompson_resume.pdf',
                'portfolio_url' => 'https://alexthompson.dev',
                'linkedin_url' => 'https://linkedin.com/in/alexthompson',
                'experience' => json_encode([
                    [
                        'company' => 'TechCorp Inc.',
                        'position' => 'Senior Frontend Developer',
                        'duration' => '2020-2025',
                        'description' => 'Led frontend development for multiple web applications using Vue.js and React'
                    ],
                    [
                        'company' => 'WebSolutions Ltd.',
                        'position' => 'Frontend Developer',
                        'duration' => '2018-2020',
                        'description' => 'Developed responsive web applications and maintained existing codebases'
                    ]
                ]),
                'education' => json_encode([
                    [
                        'institution' => 'University of California',
                        'degree' => 'Bachelor of Science in Computer Science',
                        'year' => '2018'
                    ]
                ]),
                'skills' => json_encode(['Vue.js', 'React', 'JavaScript', 'TypeScript', 'HTML5', 'CSS3', 'Git', 'Webpack']),
                'certifications' => json_encode(['AWS Certified Developer', 'Google Cloud Professional']),
                'status' => 'screening',
                'ai_score' => 85,
                'ai_analysis' => 'Strong technical background with excellent Vue.js and React experience. Good portfolio and relevant certifications. Cover letter shows enthusiasm and clear communication skills.',
                'ai_recommendations' => 'Proceed to technical interview. Focus on Vue.js architecture and modern development practices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vacancy_id' => 1, // Senior Frontend Developer
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@email.com',
                'phone' => '+1-555-1002',
                'cover_letter' => 'I am writing to express my interest in the Senior Frontend Developer position. With 7 years of experience in frontend development and a strong focus on user experience, I believe I would be a great fit for your team. I have extensive experience with modern JavaScript frameworks and a passion for creating accessible, performant web applications.',
                'resume_path' => '/storage/resumes/maria_garcia_resume.pdf',
                'portfolio_url' => 'https://mariagarcia.dev',
                'linkedin_url' => 'https://linkedin.com/in/mariagarcia',
                'experience' => json_encode([
                    [
                        'company' => 'Digital Innovations',
                        'position' => 'Lead Frontend Developer',
                        'duration' => '2021-2025',
                        'description' => 'Led a team of 4 frontend developers and architected scalable web applications'
                    ],
                    [
                        'company' => 'StartupXYZ',
                        'position' => 'Frontend Developer',
                        'duration' => '2018-2021',
                        'description' => 'Developed and maintained multiple client projects using React and Vue.js'
                    ]
                ]),
                'education' => json_encode([
                    [
                        'institution' => 'Stanford University',
                        'degree' => 'Master of Science in Computer Science',
                        'year' => '2018'
                    ]
                ]),
                'skills' => json_encode(['Vue.js', 'React', 'Angular', 'JavaScript', 'TypeScript', 'Node.js', 'GraphQL', 'Docker']),
                'certifications' => json_encode(['AWS Solutions Architect', 'Google Cloud Professional', 'Certified Scrum Master']),
                'status' => 'interview',
                'ai_score' => 92,
                'ai_analysis' => 'Exceptional candidate with strong leadership experience and advanced technical skills. Master\'s degree and multiple certifications demonstrate commitment to professional development.',
                'ai_recommendations' => 'High priority candidate. Schedule technical and leadership interviews. Excellent cultural fit potential.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vacancy_id' => 2, // Marketing Specialist
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.wilson@email.com',
                'phone' => '+1-555-2001',
                'cover_letter' => 'I am excited to apply for the Marketing Specialist position. With 4 years of experience in digital marketing and a proven track record of driving growth through data-driven campaigns, I am confident I can contribute to your marketing team\'s success.',
                'resume_path' => '/storage/resumes/james_wilson_resume.pdf',
                'portfolio_url' => 'https://jameswilsonmarketing.com',
                'linkedin_url' => 'https://linkedin.com/in/jameswilson',
                'experience' => json_encode([
                    [
                        'company' => 'Marketing Pro Agency',
                        'position' => 'Digital Marketing Specialist',
                        'duration' => '2021-2025',
                        'description' => 'Managed digital marketing campaigns for 15+ clients, achieving 40% average growth in lead generation'
                    ]
                ]),
                'education' => json_encode([
                    [
                        'institution' => 'New York University',
                        'degree' => 'Bachelor of Arts in Marketing',
                        'year' => '2021'
                    ]
                ]),
                'skills' => json_encode(['Google Analytics', 'Google Ads', 'Facebook Ads', 'Email Marketing', 'Content Marketing', 'SEO', 'HubSpot']),
                'certifications' => json_encode(['Google Analytics Certified', 'HubSpot Content Marketing', 'Facebook Blueprint']),
                'status' => 'submitted',
                'ai_score' => 78,
                'ai_analysis' => 'Good marketing background with relevant experience in digital marketing. Strong analytical skills and relevant certifications. Cover letter demonstrates understanding of marketing metrics.',
                'ai_recommendations' => 'Proceed to initial screening. Focus on campaign performance and analytical capabilities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hr_applications')->insert($applications);
    }
}
