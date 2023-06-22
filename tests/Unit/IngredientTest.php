<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class IngredientTest extends TestCase
{
    use CreatesApplication, WithFaker;

    public function testIndex()
    {
        Ingredient::factory()->count(5)->create();

        $response = $this->get('/ingredients');

        $response->assertStatus(200);
        $response->assertViewIs('ingredients.index');
        $response->assertViewHas('ingredients');
    }

    public function testCreate()
    {
        $response = $this->get('/ingredients/create');

        $response->assertStatus(200);
        $response->assertViewIs('ingredients.create');
    }

    public function testStore()
    {
        $ingredientData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];

        $response = $this->post('/ingredients', $ingredientData);

        $response->assertRedirect('/ingredients');
        $this->assertDatabaseHas('ingredients', $ingredientData);
    }

    public function testEdit()
    {
        $ingredient = Ingredient::factory()->create();

        $response = $this->get('/ingredients/'.$ingredient->id.'/edit');

        $response->assertStatus(200);
        $response->assertViewIs('ingredients.edit');
        $response->assertViewHas('ingredient', $ingredient);
    }

    public function testUpdate()
    {
        $ingredient = Ingredient::factory()->create();

        $updatedData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];

        $response = $this->put('/ingredients/'.$ingredient->id, $updatedData);

        $response->assertRedirect('/ingredients');
        $this->assertDatabaseHas('ingredients', $updatedData);
    }

    public function testDestroy()
    {
        $ingredient = Ingredient::factory()->create();

        $response = $this->delete('/ingredients/'.$ingredient->id);

        $response->assertRedirect('/ingredients');
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }
}
