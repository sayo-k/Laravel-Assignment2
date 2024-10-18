@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Are you ready to dance, {{ $user->name }}? <br> You are our {{ $user->role }}!!</h1>
    <h2>Your Courses:</h2>
    <ul class="list-group">
        @if($courses && $courses->isNotEmpty())
            @foreach($courses as $course)
                <li class="list-group-item">
                    <a href="{{ route('courses.show', $course->id) }}">
                        {{ $course->course_code }} - {{ $course->name }}
                    </a>
                </li>
            @endforeach
        @else
            @if($user->role == 'teacher')
                <p>You are not teaching any courses currently.</p>

            @elseif($user->role == 'student')
                <p>You are not enrolled in any courses currently.</p>
            @endif
        @endif

        @if($user->role == 'teacher')    
            <div class="container">
                <h4>Upload Course File</h4>

                <form action="{{ route('courses.upload') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="courseFile">Course Information File</label>
                        <input type="file" name="courseFile" id="courseFile" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload Course</button>
                </form>
            </div>
        @endif
    </ul>
</div>
@endsection