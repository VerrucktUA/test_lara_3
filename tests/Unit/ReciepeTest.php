<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateRecipeWithIngredients()
    {
        $ingredients = Ingredient::factory()->count(3)->create();

        $recipeData = [
            'name' => 'Chocolate Cake',
            'description' => 'Delicious chocolate cake recipe',
            'ingredients' => [
                [
                    'ingredient_id' => $ingredients[0]->id,
                    'count' => 2,
                    'unit' => 'cup',
                ],
                [
                    'ingredient_id' => $ingredients[1]->id,
                    'count' => 1,
                    'unit' => 'tsp',
                ],
                [
                    'ingredient_id' => $ingredients[2]->id,
                    'count' => 3,
                    'unit' => 'tablespoon',
                ],
            ],
        ];

        $response = $this->post('/recipes', $recipeData);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Recipe created successfully']);

        $recipe = Recipe::first();
        $this->assertEquals('Chocolate Cake', $recipe->name);
        $this->assertEquals('Delicious chocolate cake recipe', $recipe->description);

        $recipeIngredients = $recipe->ingredients()->get();
        $this->assertCount(3, $recipeIngredients);

        foreach ($recipeData['ingredients'] as $key => $ingredientData) {
            $this->assertEquals($ingredients[$key]->id, $recipeIngredients[$key]->ingredient_id);
            $this->assertEquals($ingredientData['count'], $recipeIngredients[$key]->count);
            $this->assertEquals($ingredientData['unit'], $recipeIngredients[$key]->unit);
        }
    }

    // Add more test methods for updating, deleting, and other recipe-related functionalities
}
