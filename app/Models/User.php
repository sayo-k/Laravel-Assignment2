<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Allow mass assignment for these fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Adding the 'role' to distinguish between teachers and students
        's_number',
    ];

    // hidden attributes for serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];


    //check if the user is a teacher
    public function isTeacher() {
        return $this->role === 'teacher';
    }
    
    //check if the user is a student
    public function isStudent() {
        return $this->role === 'student';
    }

    public function getScoreForAssessment($assessmentId)
    {
        // Query the student_assessments table to get the score for this student
        $score = DB::table('student_assessments')
            ->where('student_id', $this->id)  // Use the student's ID
            ->where('assessment_id', $assessmentId)
            ->value('score');  // Retrieve only the score

        return $score;
    }

    // Get all courses taught by a teacher, each course is taught by one teacher
    public function coursesTeaching()
    {
        return $this->hasMany(Course::class, 'teacher_id'. 'id');
    }

    // Get all courses a student is enrolled in
    public function coursesEnrolled()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id');
    }

    // Relationship for assessments a student is enrolled in
    public function assessmentsEnrolled()
    {
        return $this->belongsToMany(Assessment::class, 'student_assessments', 'student_id', 'assessment_id');
    }

    public function reviewRatings()
    {
        return $this->hasMany(ReviewRating::class, 'reviewer_id');
    }

    public function receivedRatings()
    {
        return $this->hasMany(ReviewRating::class, 'reviewee_id'); // 'reviewee_id' points to the user who received the ratings
    }
}