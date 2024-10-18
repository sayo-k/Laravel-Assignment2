<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $assessments = [
            [
                'title' => 'Ballet Fundamentals Exam',
                'instruction' => 'Perform basic ballet positions and movements.',
                'required_reviews' => 3,
                'course_id' => 1,
                'due_date' => '2024-10-15',
                'type' => 'teacher-assign',
                'max_score' => 100
            ],
            [
                'title' => 'Hip Hop Choreography Review',
                'instruction' => 'Submit a 1-minute hip hop routine.',
                'required_reviews' => 2,
                'course_id' => 2,
                'due_date' => '2024-10-10',
                'type' => 'student-select',
                'max_score' => 10
            ],
            [
                'title' => 'Contemporary Dance Interpretation',
                'instruction' => 'Perform a 2-minute contemporary piece.',
                'required_reviews' => 3,
                'course_id' => 3,
                'due_date' => '2024-10-20',
                'type' => 'student-select',
                'max_score' => 30
            ],
            [
                'title' => 'Salsa & Bachata Partner Dance Evaluation',
                'instruction' => 'Perform a partner dance routine in salsa or bachata.',
                'required_reviews' => 3,
                'course_id' => 4,
                'due_date' => '2024-10-25',
                'type' => 'teacher-assign',
                'max_score' => 40
            ],
            [
                'title' => 'Jazz Dance Combo Assessment',
                'instruction' => 'Perform a jazz dance combination learned in class.',
                'required_reviews' => 2,
                'course_id' => 5,
                'due_date' => '2024-10-30',
                'type' => 'teacher-assign',
                'max_score' => 50
            ],
            [
                'title' => 'Tap Dance Routine Submission',
                'instruction' => 'Submit a 2-minute tap dance routine.',
                'required_reviews' => 3,
                'course_id' => 6,
                'due_date' => '2024-11-01',
                'type' => 'student-select',
                'max_score' => 60
            ],
            [
                'title' => 'Modern Dance Freestyle',
                'instruction' => 'Perform a freestyle modern dance routine.',
                'required_reviews' => 3,
                'course_id' => 7,
                'due_date' => '2024-11-05',
                'type' => 'teacher-assign',
                'max_score' => 70
            ],
            [
                'title' => 'Broadway Jazz Combo',
                'instruction' => 'Submit a Broadway jazz combination learned in class.',
                'required_reviews' => 2,
                'course_id' => 8,
                'due_date' => '2024-11-10',
                'type' => 'teacher-assign',
                'max_score' => 80
            ],
            [
                'title' => 'Dance Improvisation Assessment',
                'instruction' => 'Perform an improvisation routine in any dance style.',
                'required_reviews' => 3,
                'course_id' => 9,
                'due_date' => '2024-11-15',
                'type' => 'student-select',
                'max_score' => 90
            ],
            [
                'title' => 'Choreography Composition',
                'instruction' => 'Submit a composed choreography of 2-3 minutes.',
                'required_reviews' => 3,
                'course_id' => 10,
                'due_date' => '2024-11-20',
                'type' => 'teacher-assign',
                'max_score' => 100
            ],
            [
                'title' => 'Acro Dance Routine Assessment',
                'instruction' => 'Submit a 1-minute acro dance routine.',
                'required_reviews' => 3,
                'course_id' => 1,
                'due_date' => '2024-11-25',
                'type' => 'student-select',
                'max_score' => 85
            ],
            [
                'title' => 'Lyrical Dance Expression',
                'instruction' => 'Perform a lyrical dance expressing emotions through movement.',
                'required_reviews' => 2,
                'course_id' => 2,
                'due_date' => '2024-11-30',
                'type' => 'teacher-assign',
                'max_score' => 75
            ],
            [
                'title' => 'Pointe Work Fundamentals',
                'instruction' => 'Demonstrate basic pointe work techniques.',
                'required_reviews' => 2,
                'course_id' => 3,
                'due_date' => '2024-12-01',
                'type' => 'teacher-assign',
                'max_score' => 70
            ],
            [
                'title' => 'Ballroom Dance Technique',
                'instruction' => 'Perform a ballroom dance routine in either waltz or tango.',
                'required_reviews' => 3,
                'course_id' => 4,
                'due_date' => '2024-12-05',
                'type' => 'student-select',
                'max_score' => 65
            ],
            [
                'title' => 'Dance Production Project',
                'instruction' => 'Create a 3-minute dance production project.',
                'required_reviews' => 3,
                'course_id' => 5,
                'due_date' => '2024-12-10',
                'type' => 'teacher-assign',
                'max_score' => 100
            ],
            [
                'title' => 'Solo Jazz Performance',
                'instruction' => 'Submit a solo jazz dance performance of 2 minutes.',
                'required_reviews' => 2,
                'course_id' => 6,
                'due_date' => '2024-12-15',
                'type' => 'teacher-assign',
                'max_score' => 50
            ],
            [
                'title' => 'Dance Theory Examination',
                'instruction' => 'Complete an exam on dance theory and history.',
                'required_reviews' => 0,
                'course_id' => 7,
                'due_date' => '2024-12-20',
                'type' => 'teacher-assign',
                'max_score' => 100
            ],
            [
                'title' => 'Advanced Ballet Combinations',
                'instruction' => 'Perform advanced ballet combinations.',
                'required_reviews' => 3,
                'course_id' => 1,
                'due_date' => '2024-12-22',
                'type' => 'student-select',
                'max_score' => 100
            ],
            [
                'title' => 'Hip Hop Battle Challenge',
                'instruction' => 'Compete in a hip hop dance battle.',
                'required_reviews' => 3,
                'course_id' => 2,
                'due_date' => '2024-12-25',
                'type' => 'student-select',
                'max_score' => 20
            ],
            [
                'title' => 'Musical Theatre Dance Showcase',
                'instruction' => 'Perform a 2-minute musical theatre dance routine.',
                'required_reviews' => 3,
                'course_id' => 8,
                'due_date' => '2024-12-30',
                'type' => 'teacher-assign',
                'max_score' => 75
            ],
            [
                'title' => 'Choreographic Techniques',
                'instruction' => 'Demonstrate key choreographic techniques in a 1-minute routine.',
                'required_reviews' => 3,
                'course_id' => 9,
                'due_date' => '2025-01-05',
                'type' => 'teacher-assign',
                'max_score' => 80
            ],
            [
                'title' => 'Improvisation Challenge',
                'instruction' => 'Submit a 1-minute improvisation dance.',
                'required_reviews' => 2,
                'course_id' => 10,
                'due_date' => '2025-01-10',
                'type' => 'student-select',
                'max_score' => 60
            ],
            [
                'title' => 'Cultural Dance Presentation',
                'instruction' => 'Perform a cultural dance from your region.',
                'required_reviews' => 3,
                'course_id' => 4,
                'due_date' => '2025-01-15',
                'type' => 'teacher-assign',
                'max_score' => 90
            ],
            [
                'title' => 'Street Dance Routine Submission',
                'instruction' => 'Submit a street dance routine for assessment.',
                'required_reviews' => 2,
                'course_id' => 3,
                'due_date' => '2025-01-20',
                'type' => 'student-select',
                'max_score' => 45
            ],
            [
                'title' => 'Partnering Techniques Assessment',
                'instruction' => 'Demonstrate partnering techniques with a classmate.',
                'required_reviews' => 3,
                'course_id' => 5,
                'due_date' => '2025-01-25',
                'type' => 'teacher-assign',
                'max_score' => 85
            ],
            [
                'title' => 'Historical Dance Research',
                'instruction' => 'Submit a research paper on historical dance forms.',
                'required_reviews' => 0,
                'course_id' => 6,
                'due_date' => '2025-01-30',
                'type' => 'teacher-assign',
                'max_score' => 70
            ],
            [
                'title' => 'Freestyle Dance Competition',
                'instruction' => 'Submit a 2-minute freestyle dance video.',
                'required_reviews' => 3,
                'course_id' => 7,
                'due_date' => '2025-02-05',
                'type' => 'student-select',
                'max_score' => 60
            ],
            [
                'title' => 'Dance Technique Examination',
                'instruction' => 'Complete an exam testing your dance techniques and styles.',
                'required_reviews' => 0,
                'course_id' => 8,
                'due_date' => '2025-02-10',
                'type' => 'teacher-assign',
                'max_score' => 100
            ],
            [
                'title' => 'Choreography Masterclass Submission',
                'instruction' => 'Submit choreography for a masterclass presentation.',
                'required_reviews' => 3,
                'course_id' => 9,
                'due_date' => '2025-02-15',
                'type' => 'teacher-assign',
                'max_score' => 85
            ]
        ];

        foreach ($assessments as $assessment) {
            DB::table('assessments')->insert([
                'title' => $assessment['title'],
                'instruction' => $assessment['instruction'],
                'required_reviews' => $assessment['required_reviews'],
                'course_id' => $assessment['course_id'],
                'due_date' => $assessment['due_date'],
                'type' => $assessment['type'],
                'max_score' => $assessment['max_score'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
