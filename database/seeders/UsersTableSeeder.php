<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Check the database connection type
        if (DB::getDriverName() !== 'sqlite') {
            // Disable foreign key checks for databases other than SQLite
            Schema::disableForeignKeyConstraints();
        }

        // Clear the users table before seeding
        DB::table('users')->truncate(); 

        $faker = Faker::create();
        
        // Insert Teachers
        $teachers = [
            ['name' => 'Mickey Mouse', 'email' => 'mickey.mouse@disney.com'],
            ['name' => 'Donald Duck', 'email' => 'donald.duck@disney.com'],
            ['name' => 'Goofy Goof', 'email' => 'goofy.goof@disney.com'],
            ['name' => 'Minnie Mouse', 'email' => 'minnie.mouse@disney.com'],
            ['name' => 'Daisy Duck', 'email' => 'daisy.duck@disney.com'],
        ];

        foreach ($teachers as $index => $teacher) {
            // Generate unique s_number for teachers in the format S9XXXXXXX
            $s_number = 'S9' . str_pad($index + 1, 7, '0', STR_PAD_LEFT); // Example: S9000001

            DB::table('users')->insertOrIgnore([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'role' => 'teacher',
                's_number' => $s_number,
                'password' =>  Hash::make('awsomeDancing'), // Assuming a default password & hashed in models
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get all course IDs for enrollment and teacher IDs
        //$courses = DB::table('courses')->pluck('id');
        $teacher_ids = DB::table('users')->where('role', 'teacher')->pluck('id');
         
        // Insert Students
        for ($i = 0; $i < 50; $i++) {
            // Generate unique student number in the format SXXXXXXX
            $s_number = 'S1' . str_pad($i + 1, 7, '0', STR_PAD_LEFT); // S1234567
            
            // Create a student
            $student_id = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'role' => 'student',
                's_number' => $s_number,
                'password' => Hash::make('time2Dance'), // Assuming a default password & hashed in models
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Enroll the student in 1 to 5 random courses
            //$enrollments = $courses->random(rand(1, 5))->all();
            //foreach ($enrollments as $course_id) {
            //    DB::table('enrollments')->insert([
            //        'student_id' => $student_id,
            //        'course_id' => $course_id,
            //        'created_at' => now(),
            //        'updated_at' => now(),
            //    ]);
            //}

            // Re-enable foreign key checks only if the database is not SQLite
            if (DB::getDriverName() !== 'sqlite') {
                Schema::enableForeignKeyConstraints();}
        }
    }
}
