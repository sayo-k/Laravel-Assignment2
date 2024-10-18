@extends('layouts.master')

@section('content')
<div class="container">
    @foreach($topReviewers as $reviewer)
            <div>
                <h3>{{ $reviewer->name }} - RIS: {{ number_format($reviewer->ris, 2) }}</h3>
            </div>
    @endforeach
    </div>
@endsection