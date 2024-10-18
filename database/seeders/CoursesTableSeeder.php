<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate the tables
        DB::table('courses')->truncate();
        DB::table('enrollments')->truncate();
        
        // Get the teacher IDs from the users table
        $teachers = DB::table('users')->where('role', 'teacher')->pluck('id')->toArray();

        // Courses data with respective teacher index
        $courses = [
            ['course_code' => 'DS101', 'name' => 'Introduction to Ballet', 'teacher_id' => $teachers[0] ?? null],
            ['course_code' => 'DS102', 'name' => 'Hip Hop Basics', 'teacher_id' => $teachers[1] ?? null],
            ['course_code' => 'DS103', 'name' => 'Contemporary Dance Techniques', 'teacher_id' => $teachers[2] ?? null],
            ['course_code' => 'DS104', 'name' => 'Latin Dance: Salsa & Bachata', 'teacher_id' => $teachers[3] ?? null],
            ['course_code' => 'DS105', 'name' => 'Jazz Dance Fundamentals', 'teacher_id' => $teachers[4] ?? null],
            ['course_code' => 'DS106', 'name' => 'Tap Dance: Rhythm & Music', 'teacher_id' => $teachers[0] ?? null],
            ['course_code' => 'DS107', 'name' => 'Modern Dance Styles', 'teacher_id' => $teachers[1] ?? null],
            ['course_code' => 'DS108', 'name' => 'Broadway Dance Techniques', 'teacher_id' => $teachers[2] ?? null],
            ['course_code' => 'DS109', 'name' => 'Improvisation in Dance', 'teacher_id' => $teachers[2] ?? null],
            ['course_code' => 'DS110', 'name' => 'Choreography and Composition', 'teacher_id' => $teachers[3] ?? null],
        ];


        //Course::create(...). This takes advantage of Eloquent's mass assignment and automatically handles the timestamps
        // Loop through the courses array and create each course
        foreach ($courses as $course) {
            if ($course['teacher_id']) { // Check if teacher_id is valid
                Course::create([
                    'course_code' => $course['course_code'],
                    'name' => $course['name'],
                    'teacher_id' => $course['teacher_id'],
                ]);
            } else {
                // Handle case where teacher_id is not available
                throw new \Exception("Teacher ID not found for course: " . $course['name']);
            }
        }
    }
}