@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Assign Reviewers for Assessment: {{ $assessment->title }}</h1>

        <form action="{{ route('reviews.teacher-assign', $assessment->id) }}" method="POST" novalidate>
        @csrf
        <h3>Groups of {{ $assessment->required_reviews + 1 }} students</h3>

        @foreach($groups as $groupIndex => $group)
            <h4>Group {{ $groupIndex + 1 }}</h4>
            <ul>
                @foreach($group as $student)
                    <li>
                        {{ $student->name }}
                        <input type="hidden" name="groups[{{ $groupIndex }}][]" value="{{ $student->id }}">
                    </li>
                @endforeach
            </ul>
        @endforeach

        <button type="submit" class="btn btn-primary">Confirm Groups and Assign Reviews</button>
    </form>
    </div>
@endsection

