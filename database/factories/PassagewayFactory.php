<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PassagewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['1号门', '2号门', '3号门', '4号门', '5号门', '行政楼', '综合楼', '实验楼']),
            'note' => $this->faker->sentence
        ];
    }
}
