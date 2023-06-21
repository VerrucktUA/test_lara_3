@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Ingredient</h1>
        <form action="{{ route('ingredients.store') }}" method="POST">
            @csrf
            @if (isset($ingredient))
                @method('PUT')
            @endif
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="name">Description:</label>
                <input type="text" name="description" id="description" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
