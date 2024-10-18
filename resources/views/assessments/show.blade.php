@extends('layouts.master')
<!--not using 
@section('content')
<div class="container">
    <h1>Assessment Details</h1>

    <div class="card">
        <div class="card-header">
            {{ $assessment->title }}
        </div>
        <div class="card-body">
            <h5 class="card-title">Instructions</h5>
            <p class="card-text">{{ $assessment->instruction }}</p>

            <h5 class="card-title">Details</h5>
            <p class="card-text"><strong>Required Reviews:</strong> {{ $assessment->required_reviews }}</p>
            <p class="card-text"><strong>Max Score:</strong> {{ $assessment->max_score }}</p>
            <p class="card-text"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('Y-m-d H:i') }}</p>
            <p class="card-text"><strong>Type:</strong> {{ $assessment->type }}</p>
        </div>
    </div>

    <a href="{{ route('assessments.edit', $assessment->id) }}" class="btn btn-primary mt-3">Edit Assessment</a>
    <a href="{{ route('courses.show', $assessment->course_id) }}" class="btn btn-secondary mt-3">Back to Course</a>
</div>
@endsection-->