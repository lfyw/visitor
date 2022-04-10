<?php

namespace Database\Factories;

use App\Models\Gate;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassingLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_card' => Visitor::inRandomOrder()->first()?->id_card ?: 'test',
            'gate_id' => Gate::inRandomOrder()->first()?->id ?: 0,
            'passed_at' => now()
        ];
    }
}
