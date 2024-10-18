<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'instruction',
        'required_reviews',
        'max_score',
        'course_id',
        'due_date',
        'type',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function enrolledStudents()
    {
        return $this->course->students()->where('role', 'student'); // Filter only users with 'student' role
    }
}
