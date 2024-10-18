@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Reviews for {{ $student->name }} - Assessment: {{ $assessment->title }}</h1>

        @if($reviews->isEmpty())
            <p>No reviews submitted by {{ $student->name }} for this assessment.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>Reviewee</th>
                    <th>Review Text</th>
                    <th>Date Submitted</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr>
                        <td>{{ $review->reviewer->name }}</td>  <!-- Reviewer's name -->
                        <td>{{ $review->reviewee->name }}</td>  <!-- Reviewee's name -->
                        <td>{{ $review->review_text }}</td>      <!-- Actual review text -->
                        <td>{{ $review->created_at->format('d M Y') }}</td>  <!-- Date submitted -->
                    </tr>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($reviewsReceived->isEmpty())
        <p>No reviews received by {{ $student->name }} for this assessment.</p>
        @else
        <table class="table">
            <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>Review Text</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviewsReceived as $review)
                    <tr>
                        <td>{{ $review->reviewer->name }}</td>  <!-- Reviewer (who submitted the review) -->
                        <td>{{ $review->review_text }}</td>    <!-- Actual review text -->
                        <td>{{ $review->created_at->format('d M Y') }}</td>  <!-- Date submitted -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    </div>

    <h2>Enrolled Students - Score Update</h2>
    <table class="table">
            <thead>
            <tr>
                <th>Student Name</th>
                <th>Score</th>
                <th>Update Score</th>
                </tr>
            </thead>
            <tbody>

        <tr>
            <td>{{ $student->name }}</td>
            <td>{{ $student->getScoreForAssessment($assessment->id) }}</td>
            <td>
                <form action="{{ route('scores.update', [$assessment->id, $student->id]) }}" method="POST" novalidate>
                    @csrf
                    @method('PATCH')
                    <input type="number" name="score" value="{{ $student->score }}" min="0" max="{{ $assessment->max_score }}" required> 
                    <button type="submit" class="btn btn-success btn-sm">Update Score</button>
                </form>
            </td>
            
        </tr>
    </table>
        <td>
            <a href="{{ route('assessments.teacher_show', $assessment->id) }}" class="btn btn-info btn-sm">Back to Assessment</a>
        </td>
@endsection
