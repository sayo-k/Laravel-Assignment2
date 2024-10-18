<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get all student and course IDs
        $student_ids = DB::table('users')->where('role', 'student')->pluck('id');
        $course_ids = DB::table('courses')->pluck('id');

        foreach ($student_ids as $student_id) {
            // Enroll each student in 1 to 5 random courses
            $enrollments = $course_ids->random(rand(1, 5))->all();
            foreach ($enrollments as $course_id) {
                
                DB::table('enrollments')->insert([
                    'student_id' => $student_id,
                    'course_id' => $course_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
