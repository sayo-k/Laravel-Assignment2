<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'reviewer_id',
        'reviewee_id',
        'clarity_rating',
        'constructiveness_rating',
        'specificity_rating',
        'tone_rating'
    ];

    // Relationship to the Review
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Relationship to the Reviewer (User)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Relationship to the Reviewee (User)
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}