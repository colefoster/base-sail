<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->word(),
            "api_id" => $this->faker->unique()->numberBetween(1, 1000),
            "cost" => $this->faker->numberBetween(10, 1000),
            "fling_power" => $this->faker->numberBetween(0, 150),
            "fling_effect" => $this->faker->word(),
            "category" => $this->faker->word(),
        ];
    }
}
