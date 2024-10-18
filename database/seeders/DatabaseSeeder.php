<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // the order to imprement seeders
        $this->call([
            UsersTableSeeder::class,
            CoursesTableSeeder::class,
            //UsersTableSeeder::class,  
            AssessmentsTableSeeder::class,
            EnrollmentsTableSeeder::class,   
        ]);
    }
}
