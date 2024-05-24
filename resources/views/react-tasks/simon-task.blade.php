@extends('layouts.music-task')

@section('title', 'Music Task')

@section('styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('vite.svg') }}" />
    <link rel="stylesheet" crossorigin href="{{ asset('simon-task/assets/index-RMzLglGB.css') }}">
@endsection

@section('content')
    <div id="root"></div>
@endsection

@section('scripts')
    <script type="module" crossorigin src="{{ asset('simon-task/assets/index-W2uaDbp3.js') }}"></script>
@endsection
