@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($recipe)
            <h1>{{ $recipe->name }}</h1>
            <p>{{ $recipe->description }}</p>
            <p>Owner: {{ $recipe->user ? $recipe->user->name : 'N/A' }}</p>
            <p>Status: {{ $recipe->status ? 'Public' : 'Private' }}</p>

            <h2>Ingredients</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Count</th>
                    <th>Unit</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recipe->ingredients as $ingredient)
                    <tr>
                        <td>{{ $ingredient->name }}</td>
                        <td>{{ $ingredient->pivot->count }}</td>
                        <td>{{ $ingredient->pivot->unit }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <a href="{{ route('recipes.index') }}" class="btn btn-primary">Back</a>
        @else
            <p>Recipe not found.</p>
        @endif
    </div>
@endsection
