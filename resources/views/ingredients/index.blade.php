@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>All Ingredients</h1>
        <div class="mb-3">
            <a href="{{ route('ingredients.create') }}" class="btn btn-success">Create New Ingredient</a>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->name }}</td>
                    <td>
                        <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" style="display: inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this ingredient?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
