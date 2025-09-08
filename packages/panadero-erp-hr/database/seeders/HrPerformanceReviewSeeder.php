<?php

namespace Panadero\Erp\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrPerformanceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'employee_id' => 1, // John Smith
                'reviewer_id' => 1,
                'review_period' => 'Q3 2025',
                'review_date' => '2025-09-30',
                'period_start' => '2025-07-01',
                'period_end' => '2025-09-30',
                'goals' => json_encode([
                    'Complete recruitment for 5 open positions',
                    'Implement new HRIS system',
                    'Conduct 20 performance reviews',
                    'Improve employee satisfaction scores by 10%'
                ]),
                'achievements' => json_encode([
                    'Successfully recruited 6 candidates (exceeded goal)',
                    'HRIS implementation completed on time',
                    'Conducted 25 performance reviews',
                    'Employee satisfaction improved by 15%'
                ]),
                'skills_assessment' => json_encode([
                    'Recruitment' => 5,
                    'Employee Relations' => 4,
                    'Performance Management' => 5,
                    'HRIS Management' => 4,
                    'Communication' => 5
                ]),
                'overall_rating' => 5,
                'strengths' => 'Excellent recruitment skills, strong communication with employees, proactive approach to problem-solving, great team leadership.',
                'areas_for_improvement' => 'Continue developing technical HRIS skills, work on time management during peak recruitment periods.',
                'manager_comments' => 'John has exceeded expectations this quarter. His recruitment success rate is outstanding and he has been instrumental in improving our HR processes.',
                'employee_comments' => 'I am proud of the team\'s achievements this quarter. I look forward to continuing to improve our HR processes and employee experience.',
                'development_plan' => json_encode([
                    'Complete advanced HRIS training',
                    'Attend SHRM conference',
                    'Mentor junior HR staff',
                    'Lead employee engagement initiative'
                ]),
                'status' => 'completed',
                'employee_acknowledged_at' => '2025-10-01 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2, // Sarah Johnson
                'reviewer_id' => 1,
                'review_period' => 'Q3 2025',
                'review_date' => '2025-09-30',
                'period_start' => '2025-07-01',
                'period_end' => '2025-09-30',
                'goals' => json_encode([
                    'Complete 3 major feature developments',
                    'Improve code quality metrics',
                    'Mentor 2 junior developers',
                    'Reduce bug reports by 20%'
                ]),
                'achievements' => json_encode([
                    'Completed 4 major features (exceeded goal)',
                    'Code quality score improved to 95%',
                    'Successfully mentored 3 junior developers',
                    'Bug reports reduced by 30%'
                ]),
                'skills_assessment' => json_encode([
                    'JavaScript' => 5,
                    'Vue.js' => 5,
                    'Code Review' => 5,
                    'Mentoring' => 4,
                    'Problem Solving' => 5
                ]),
                'overall_rating' => 5,
                'strengths' => 'Exceptional technical skills, excellent code quality, great mentoring abilities, proactive in identifying and solving problems.',
                'areas_for_improvement' => 'Continue developing leadership skills, work on project management capabilities.',
                'manager_comments' => 'Sarah is an outstanding developer who consistently delivers high-quality work. Her mentoring has been invaluable to the team.',
                'employee_comments' => 'I enjoyed the challenges this quarter and am proud of the team\'s progress. I look forward to taking on more leadership responsibilities.',
                'development_plan' => json_encode([
                    'Complete technical leadership training',
                    'Lead a major project',
                    'Present at tech conference',
                    'Develop project management skills'
                ]),
                'status' => 'completed',
                'employee_acknowledged_at' => '2025-10-01 14:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hr_performance_reviews')->insert($reviews);
    }
}
