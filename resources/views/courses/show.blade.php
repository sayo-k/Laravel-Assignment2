@extends('layouts.master')

@section('title', 'Course Details')

@section('content')
    <div class="container">
        <h1>{{ $course->name }}</h1>
        <p><strong>course_code: </strong>{{ $course->course_code }}</p>
        <p><strong>Instructors: </strong>{{$course->teacher->name}}</p>

        <h2>Peer Review Assessments</h2>
        <ul>
            @foreach ($course->assessments as $assessment)
                <li>
                    @if(auth()->user()->isTeacher())
                        <a href="{{ route('assessments.teacher_show', $assessment->id) }}">{{ $assessment->title }}</a>
                    @else
                        <a href="{{ route('assessments.student_show', $assessment->id) }}">{{ $assessment->title }}</a>
                    @endif
                    - Due: {{ \Carbon\Carbon::parse($assessment->due_date)->format('M d, Y') }}<br><br>
                </li>
            @endforeach

            @if($user->role === 'teacher')
                <form action="{{ route('courses.enroll', $courseId) }}" method="POST" novalidate>
                    @csrf

                    <h4>Add Studetns to Enroll</h4>
                    <select name="student_id">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->s_number }})</option>
                        @endforeach
                    </select><br><br>
                    <button type="submit">Enroll Student</button>
                </form><br><br>
                

                <h4>Add Peer Review Assessment</h4>
                <form action="{{ route('assessments.store') }}" method="POST" novalidate>
                <!--<form action="{{ route('assessments.store', $courseId) }}" method="POST">-->
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}" required>
                    <label for="title">Assessment Title (max 20 characters):</label><br>
                    <input type="text" name="title" maxlength="20" required><br>

                    <label for="instruction">Instruction:</label><br>
                    <textarea name="instruction" required></textarea><br>

                    <label for="required_reviews">Number of Reviews Required:</label><br>
                    <input type="number" name="required_reviews" min="1" required><br>

                    <label for="max_score">Maximum Score:</label><br>
                    <input type="number" name="max_score" min="1" max="100" required><br>

                    <label for="due_date">Due Date and Time:</label><br>
                    <input type="datetime-local" name="due_date" required><br>

                    <label for="type">Review Type:</label><br>
                    <select name="type" required><br>
                        <option value="student-select">Student-Select</option>
                        <option value="teacher-assign">Teacher-Assign</option>
                    </select><br><br>

                    <button type="submit">Add Assessment</button>
                </form>
            @endif
        </ul>
    </div>
@endsection
