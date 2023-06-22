<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RecipeTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function testIndex()
    {
        // Create a user
        $user = User::factory()->create();

        // Create recipes by the user
        $recipesByOwner = Recipe::factory()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        // Create public recipes
        $publicRecipes = Recipe::factory()->count(2)->create([
            'status' => true,
        ]);

        // Send a GET request to the index route
        $response = $this->actingAs($user)->get(route('recipes.index'));

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the view has the correct data
        $response->assertViewHas('recipesByOwner', $recipesByOwner);
        $response->assertViewHas('publicRecipes', $publicRecipes);
    }

    public function testCreate()
    {
        Ingredient::factory()->count(5)->create();

        $response = $this->get('/recipes/create');

        $response->assertStatus(200);
    }

    public function testStore()
    {
        // Create a user
        $user = factory(App\Models\User::class)->create();

        // Create ingredients
        $ingredients = factory(App\Models\Ingredient::class, 3)->create();

        // Generate the request payload
        $payload = [
            'name' => 'Test Recipe',
            'description' => 'This is a test recipe',
            'ingredients' => [
                [
                    'id' => $ingredients[0]->id,
                    'count' => 2,
                    'unit' => 'g',
                ],
                [
                    'id' => $ingredients[1]->id,
                    'count' => 1,
                    'unit' => 'l',
                ],
            ],
        ];

        // Send a POST request to the store route
        $response = $this->actingAs($user)
            ->post(route('recipes.store'), $payload);

        // Assert that the recipe was created successfully
        $response->assertStatus(302);
        $response->assertRedirect(route('recipes.index'));
        $this->assertDatabaseHas('recipes', [
            'name' => 'Test Recipe',
            'description' => 'This is a test recipe',
            'owner_id' => $user->id,
        ]);

        // Assert that the recipe ingredients were created
        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => 1, // Assuming the recipe ID is 1
            'ingredient_id' => $ingredients[0]->id,
            'count' => 2,
            'unit' => 'g',
        ]);
        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => 1, // Assuming the recipe ID is 1
            'ingredient_id' => $ingredients[1]->id,
            'count' => 1,
            'unit' => 'l',
        ]);
    }

    public function testEdit()
    {
        $recipe = Recipe::factory()->create();
        $ingredients = Ingredient::factory()->count(3)->create();
        $recipeIngredients = RecipeIngredient::factory()->count(2)->create(['recipe_id' => $recipe->id]);

        $response = $this->actingAs($user)->get('/recipes/' . $recipe->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('recipes.edit');
        $response->assertViewHas('recipe', $recipe);
        $response->assertViewHas('ingredients', $ingredients);
        $response->assertViewHas('recipeIngredients', $recipeIngredients);
    }

    public function testUpdate()
    {
        $recipe = Recipe::factory()->create();
        $ingredient1 = Ingredient::factory()->create();
        $ingredient2 = Ingredient::factory()->create();

        $recipeIngredient1 = RecipeIngredient::factory()->create(['recipe_id' => $recipe->id]);
        $recipeIngredient2 = RecipeIngredient::factory()->create(['recipe_id' => $recipe->id]);

        $updatedData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'existing_ingredient' => [$ingredient1->id, $ingredient2->id],
            'count' => [2, 3],
            'unit' => ['kg', 'g'],
        ];

        $response = $this->put('/recipes/' . $recipe->id, $updatedData);

        $response->assertRedirect('/recipes');
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id, 'name' => $updatedData['name']]);
        $this->assertDatabaseMissing('recipe_ingredients', ['id' => $recipeIngredient1->id]);
        $this->assertDatabaseMissing('recipe_ingredients', ['id' => $recipeIngredient2->id]);
        $this->assertDatabaseHas('recipe_ingredients', ['recipe_id' => $recipe->id, 'ingredient_id' => $ingredient1->id, 'count' => 2, 'unit' => 'kg']);
        $this->assertDatabaseHas('recipe_ingredients', ['recipe_id' => $recipe->id, 'ingredient_id' => $ingredient2->id, 'count' => 3, 'unit' => 'g']);
    }

    public function testDestroy()
    {
        $recipe = Recipe::factory()->create();
        $recipeIngredients = RecipeIngredient::factory()->count(3)->create(['recipe_id' => $recipe->id]);

        $response = $this->delete('/recipes/' . $recipe->id);

        $response->assertRedirect('/recipes');
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        foreach ($recipeIngredients as $recipeIngredient) {
            $this->assertDatabaseMissing('recipe_ingredients', ['id' => $recipeIngredient->id]);
        }
    }
}
