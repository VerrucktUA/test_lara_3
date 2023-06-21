@extends('layouts.app')
@php
    $user = auth()->user();
@endphp
@section('content')
    <div class="container">
        <h1>Create Recipe</h1>

        <form action="{{ route('recipes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Recipe Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="description">Recipe Description:</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1">
                <label class="form-check-label" for="status">
                    Public
                </label>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingredients:</label>
                <select name="ingredients[]" id="ingredients" class="form-control" multiple required>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Recipe</button>
        </form>
    </div>
@endsection
