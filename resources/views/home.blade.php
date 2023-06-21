@extends('layouts.app')

@section('content')
    <div class="container">
        @if (auth()->check())
            {{-- Retrieve the authenticated user --}}
            @php
                $user = auth()->user();
            @endphp

            {{-- Display the user's status --}}
            <p>User Status: {{ $user->id }}</p>
        @else
            <p>User not authenticated</p>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Home') }}</div>

                    <div class="card-body">
                        <p>Welcome to the home page!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
