@extends('layouts.navigation')

@section('content')
    <div class="container translate-y-1/2 text-center">
        <h1 class="text-4xl font-bold">404</h1>
        <p class="text-lg">Page not found</p>
        <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Back to home</a>
    </div>
@endsection
