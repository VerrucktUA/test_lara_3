@extends('layouts.app')

@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="container">
        <h1>{{ isset($recipe) ? 'Edit Recipe' : 'Create Recipe' }}</h1>

        <form action="{{ isset($recipe) ? route('recipes.update', $recipe->id) : route('recipes.store') }}" method="POST">
            @csrf
            @if (isset($recipe))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="name">Recipe Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ isset($recipe) ? $recipe->name : '' }}" required>
            </div>

            <div class="form-group">
                <label for="description">Recipe Description:</label>
                <textarea name="description" id="description" class="form-control" required>{{ isset($recipe) ? $recipe->description : '' }}</textarea>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ isset($recipe) && $recipe->status ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                    Public
                </label>
            </div>

            <table id="ingredients-table" class="table">
                <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Count</th>
                    <th>Unit</th>
                    @if (isset($recipe))
                        <th>Action</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @if ($recipe && isset($recipe->ingredients))
                    @foreach ($recipe->ingredients as $ingredient)
                        @php
                            $recipeIngredient = $ingredient->pivot;
                        @endphp
                        <tr>
                            <td>{{ $ingredient->name }}</td>
                            <td>
                                <input type="number" name="count[]" value="{{ $recipeIngredient->count }}" required>
                            </td>
                            <td>
                                <select name="unit[]" required>
                                    <option value="g" {{ $recipeIngredient->unit === 'g' ? 'selected' : '' }}>Grams</option>
                                    <option value="p" {{ $recipeIngredient->unit === 'p' ? 'selected' : '' }}>Pounds</option>
                                    <option value="l" {{ $recipeIngredient->unit === 'l' ? 'selected' : '' }}>Liters</option>
                                </select>
                            </td>
                            @if (isset($recipe))
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="deleteIngredientRow(this)">Delete</button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>

            <button type="button" onclick="addIngredientRow()" class="btn btn-primary">Add Ingredient</button>

            <button type="submit" class="btn btn-primary">{{ isset($recipe) ? 'Update Recipe' : 'Create Recipe' }}</button>
        </form>
    </div>
@endsection

<script>
    function addIngredientRow() {
        var table = document.getElementById("ingredients-table");
        var row = table.insertRow();
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        var ingredientSelect = document.createElement("select");
        ingredientSelect.name = "existing_ingredient[]";
        ingredientSelect.required = true;
        ingredientSelect.innerHTML = '<option value="">Select Ingredient</option>@foreach ($ingredients as $ingredient)<option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>@endforeach';

        var countInput = document.createElement("input");
        countInput.type = "number";
        countInput.name = "count[]";
        countInput.value = "";
        countInput.required = true;

        var unitSelect = document.createElement("select");
        unitSelect.name = "unit[]";
        unitSelect.required = true;
        unitSelect.innerHTML = '<option value="g">Grams</option><option value="p">Pounds</option><option value="l">Liters</option>';

        cell1.appendChild(ingredientSelect);
        cell2.appendChild(countInput);
        cell3.appendChild(unitSelect);
    }

    function deleteIngredientRow(button) {
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
</script>
