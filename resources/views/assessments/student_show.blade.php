@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $assessment->title }}</h1>

    <div class="card">
        <div class="card-header">
            Instructions
        </div>
        <div class="card-body">
            <p>{{ $assessment->instruction }}</p>
            <p><strong>Required Reviews:</strong> {{ $assessment->required_reviews }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('Y-m-d H:i') }}</p>
            <p><strong>Type:</strong> {{ $assessment->type }}</p>
        </div>
    </div><br>

    @if($assessment->type === 'student-select')
    <h2>Submit Peer Reviews</h2>
    <form action="{{ route('reviews.store', $assessment->id) }}" method="POST" novalidate>
        @csrf
        @for ($i = 0; $i < $assessment->required_reviews; $i++)
            <div id="reviews-container">
                <div class="form-group">
                    <label for="reviewee_id">Select Reviewee</label>
                    <!--[] in the name attribute >> both reviewee_id and review_text sent as arrays
                    form to handle multiple review submissions for different students.-->
                    <select name="reviewee_id[]" class="form-control" required>
                        <option value="">Select a student</option>
                        @foreach($enrolledStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="review_text">Review Text</label>
                    <textarea name="review_text[]" class="form-control" required minlength="5"></textarea>
                </div>
            </div>
        @endfor
        <button type="submit" class="btn btn-primary mt-3">Submit Reviews</button>
    </form><br><br>
    @endif

    @if($assessment->type === 'teacher-assign')

        @if($submittedReviews->isNotEmpty())
        <form action="{{ route('reviews.update', $assessment->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @foreach($submittedReviews as $submittedReview)
                <div id="reviews-container">
                    <div class="form-group">
                        <label for="reviewee_id">Reviewing: {{ $submittedReview->reviewee->name }}</label>
                        <input type="hidden" name="reviewee_id[]" value="{{ $submittedReview->reviewee->id }}">
                    </div>
                    <div class="form-group">
                        <label for="review_text">Review Text</label>
                        <textarea name="review_text[]" class="form-control" required minlength="5"></textarea>
                    </div>
                </div>
            @endforeach
            <button type="submit" class="btn btn-primary mt-3">Submit Assigned Reviews</button>
        </form><br><br>
        @else
            <p>No reviews assigned to you yet.</p>
        @endif

    @endif

    <h3>Submitted Reviews</h3>
    @foreach($submittedReviews as $review)
        <div class="review">
            <p><strong>Reviewee:</strong> {{ $review->reviewee->name }}</p>
            <p>{{ $review->review_text }}</p>
        </div>
    @endforeach
    </div> <br>

    <h2>Received Reviews</h2>
    <form action="{{ route('assessments.student_rate', $assessmentId) }}" method="POST" novalidate>
        @csrf
        @foreach($receivedReviews as $review)
            <div class="review">
                <p><strong>Reviewer:</strong> {{ $review->reviewer->name }}</p>
                <p>{{ $review->review_text }}</p>
                <input type="hidden" name="reviewee_id[{{ $review->id }}]" value="{{ $revieweeId }}">
                
                <div class="form-group">
                    <label for="clarity_rating_{{ $review->id }}">Clarity (1-5)</label>
                    <input type="number" name="clarity_rating[{{ $review->id }}]" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="constructiveness_rating_{{ $review->id }}">Constructiveness (1-5)</label>
                    <input type="number" name="constructiveness_rating[{{ $review->id }}]" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="specificity_rating_{{ $review->id }}">Specificity (1-5)</label>
                    <input type="number" name="specificity_rating[{{ $review->id }}]" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="tone_rating_{{ $review->id }}">Tone (1-5)</label>
                    <input type="number" name="tone_rating[{{ $review->id }}]" min="1" max="5" required>
                </div>
            </div>
        @endforeach
        
        <button type="submit" class="btn btn-primary mt-3">Submit Review Ratings</button>
    </form>

    <div class="mt-3">
        <a href="{{ route('courses.show', $assessment->course_id) }}" class="btn btn-secondary">Back to Course</a>
        <a href="{{ route('reviews.top-reviewer', $reviewer->id) }}" class="btn btn-secondary">Check Top Reviewers</a>
    </div>
@endsection