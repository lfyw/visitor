<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\GateRule;

class GateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => now()->format('Y-m-d') . random_int(10000, 99999),
            'type' => 'ZJXH' . random_int(10000, 99999),
            'ip' => $this->faker->ipv4,
            'location' => $this->faker->randomElement(['1号门', '2号门', '3号门', '4号门', '5号门', '行政楼', '综合楼', '实验楼']),
            'rule' => $this->faker->randomElement(GateRule::getValues()),
            'note' => $this->faker->sentence
        ];
    }
}
