<?php

namespace Database\Factories;

use App\Models\RecipeIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeIngredientFactory extends Factory
{
    protected $model = RecipeIngredient::class;

    public function definition()
    {
        return [
            'recipe_id' => function () {
                return \App\Models\Receipt::factory()->create()->id;
            },
            'ingredient_id' => function () {
                return \App\Models\Ingredient::factory()->create()->id;
            },
            'quantity' => $this->faker->randomNumber(2),
            'unit' => $this->faker->word,
        ];
    }
}
