@extends('layouts.music-task')

@section('title', 'Music Task')

@section('styles')
    <link rel="icon" type="image/svg+xml" href="{{ asset('vite.svg') }}" />
    <link rel="stylesheet" crossorigin href="{{ asset('music-task/assets/index-BWk70Yzt.css') }}">
@endsection

@section('content')
    <div id="root"></div>
@endsection

@section('scripts')
    <script type="module" crossorigin src="{{ asset('music-task/assets/index-CZqsnFVI.js') }}"></script>
@endsection
