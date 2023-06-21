<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::all();
        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        $ingredients = Ingredient::all();
        return view('recipes.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'status' => '',
            'existing_ingredient.*' => 'required',
            'count.*' => 'required',
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

        return redirect()->route('recipes.index')->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe)
    {
        $ingredients = Ingredient::all();
        return view('recipes.edit', compact('recipe', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'existing_ingredient.*' => 'required',
            'count.*' => 'required',
            'unit.*' => 'required',
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
