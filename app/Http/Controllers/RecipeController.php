<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class RecipeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $recipesByOwner = Recipe::where('owner_id', auth()->user()->id)->get();
        } else {
            $recipesByOwner = collect(); // Empty collection when not logged in
        }

        $publicRecipes = Recipe::where('status', true)->get();

        return view('recipes.index', compact('recipesByOwner', 'publicRecipes'));
    }

    public function create()
    {
        $ingredients = Ingredient::all();
        return view('recipes.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'existing_ingredient' => 'required|array|min:1', // At least one ingredient is required
            'existing_ingredient.*' => [
                'required',
                Rule::exists('ingredients', 'id'),
            ],
            'count' => 'required|array|min:1',
            'count.*' => 'required|numeric',
            'unit' => 'required|array|min:1',
            'unit.*' => 'required',
        ]);

        $recipe = Recipe::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'owner_id' => auth()->user()->id,
            'status' => $request->has('status') ? true : false,
        ]);

        $existingIngredients = $request->input('existing_ingredient');
        $counts = $request->input('count');
        $units = $request->input('unit');

        foreach ($existingIngredients as $key => $existingIngredient) {
            $ingredient = Ingredient::find($existingIngredient);

            if ($ingredient) {
                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->recipe_id = $recipe->id;
                $recipeIngredient->ingredient_id = $ingredient->id;
                $recipeIngredient->count = $counts[$key];
                $recipeIngredient->unit = $units[$key];
                $recipeIngredient->save();
            }
        }

        return redirect()->route('recipes.index')->with('success', 'Recipe created successfully.');
    }

    public function show($id)
    {
        $recipe = Recipe::findOrFail($id);
        return view('recipes.show', compact('recipe'));
    }

    public function edit($id)
    {
        $recipe = Recipe::findOrFail($id);
        $ingredients = Ingredient::all();
        $recipeIngredients = $recipe->ingredients()->get();

        return view('recipes.edit', compact('recipe', 'ingredients', 'recipeIngredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'ingredients' => 'required|array|min:1', // At least one ingredient is required
            'ingredients.*.id' => [
                'required',
                Rule::exists('ingredients', 'id'),
            ],
            'ingredients.*.count' => 'required|numeric',
            'ingredients.*.unit' => 'required',
        ]);

        $recipe->name = $request->input('name');
        $recipe->description = $request->input('description');
        $recipe->save();

        $existingIngredients = $request->input('existing_ingredient');
        $counts = $request->input('count');
        $units = $request->input('unit');

        // Remove existing recipe ingredients
        $recipe->ingredients()->detach();

        for ($i = 0; $i < count($existingIngredients); $i++) {
            $ingredient = Ingredient::find($existingIngredients[$i]);

            if ($ingredient) {
                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->recipe_id = $recipe->id;
                $recipeIngredient->ingredient_id = $ingredient->id;
                $recipeIngredient->count = $counts[$i];
                $recipeIngredient->unit = $units[$i];
                $recipeIngredient->save();
            }
        }

        return redirect()->route('recipes.index')->with('success', 'Recipe updated successfully.');
    }

    public function destroy($id)
    {
        // Find the recipe by ID
        $recipe = Recipe::findOrFail($id);

        // Get the IDs of associated recipe_ingredient records
        $recipeIngredientIds = $recipe->ingredients->pluck('pivot.id');

        // Delete the associated recipe_ingredient records
        RecipeIngredient::whereIn('id', $recipeIngredientIds)->delete();

        // Delete the recipe itself
        $recipe->delete();

        // Optionally, you can add a redirect or response here
        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully');
    }


}
