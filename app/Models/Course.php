<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'name',
        'teacher_id',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id');
    }

    public function enrolledStudents()
    {
        return $this->students()->where('role', 'student'); // Filter only users with 'student' role
    }

    public function teacher()
    {
    return $this->belongsTo(User::class);
    }
}