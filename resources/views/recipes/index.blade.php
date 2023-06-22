@extends('layouts.app')

@section('content')
    <div class="container">
        @auth
            <h1>My reciepes</h1>
            <a href="{{ route('recipes.create') }}" class="btn btn-primary mb-3">Create Recipe</a>
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recipesByOwner as $recipe)
                    <tr>
                        <td>
                            <a href="{{ route('recipes.show', $recipe->id) }}">
                                {{ $recipe->name }}
                            </a>
                        </td>
                        <td>{{ $recipe->description }}</td>
                        <td>{{ $recipe->user ? $recipe->user->name : 'N/A' }}</td>
                        <td>{{ $recipe->status ? 'Public' : 'Private' }}</td>
                        <td>
                            <a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST"
                                  style="display: inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this recipe?')">Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endauth

        <h1>Public Recipes</h1>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Owner</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($publicRecipes as $recipe)
                <tr>
                    <td>
                        <a href="{{ route('recipes.show', $recipe->id) }}">
                            {{ $recipe->name }}
                        </a>
                    </td>
                    <td>{{ $recipe->description }}</td>
                    <td>{{ $recipe->user ? $recipe->user->name : 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
