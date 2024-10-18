@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Assessment: {{ $assessment->title }}</h1><br><br>
        <h2>Edit Assessment</h2>

        <form action="{{ route('assessments.update', $assessment->id) }}" method="POST" novalidate>
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $assessment->title) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="instruction">Instruction</label>
                <textarea name="instruction" id="instruction" class="form-control" required>{{ old('instruction', $assessment->instruction) }}</textarea>
            </div>

            <div class="form-group">
                <label for="required_reviews">Required Reviews</label>
                <input type="number" name="required_reviews" id="required_reviews" value="{{ old('required_reviews', $assessment->required_reviews) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="max_score">Max Score</label>
                <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assessment->max_score) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', \Carbon\Carbon::parse($assessment->due_date)->format('Y-m-d\TH:i')) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="student-select" {{ $assessment->type == 'student-select' ? 'selected' : '' }}>Student Select</option>
                    <option value="teacher-assign" {{ $assessment->type == 'teacher-assign' ? 'selected' : '' }}>Teacher Assign</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Assessment</button><br><br>
            <a href="{{ route('courses.show', $assessment->course_id) }}" class="btn btn-secondary mt-3">Back to Course</a><br><br>
            @if ($assessment->type == 'teacher-assign')
                <a href="{{ route('reviews.teacher-assign', $assessment->course_id) }}" class="btn btn-secondary mt-3">Assign Reviews</a>
            @endif
        </form><br><br>
    </div>

    <h2>Student Review Summary</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Reviews Submitted</th>
                <th>Reviews Received</th>
                <th>Score</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrolledStudents as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>{{ $student->submittedReviewsCount }}</td>
                <td>{{ $student->receivedReviewsCount }}</td>
                <td>{{ $student->getScoreForAssessment($assessment->id) }}</td>
                <td>
                    <a href="{{ route('students.reviews', [$assessment->id, $student->id]) }}" class="btn btn-info btn-sm">View Reviews</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!--Laravel's Built-in Pagination-->
    {{-- Pagination in the middle --}}
    <div class="pagination mt-4">
        {{ $enrolledStudents->links() }} {{-- This will render pagination links --}}
    </div>

</div>
@endsection
