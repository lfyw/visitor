<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['生产区', '生活区', '行政区']),
            'note' => $this->faker->sentence
        ];
    }
}
